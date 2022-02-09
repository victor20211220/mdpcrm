<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_email extends CI_Model
{
    public function settings()
    {
        $row = $this->db->get_where('ip_email_settings', ['user_id' => $this->uri->segment(3)])->row_array();

        if ($row) {
            $data = $row;
        } else {
            $data = [
                'host'       => null,
                'username'   => null,
                'password'   => null,
                'type'       => null,
                'ssl_status' => null
            ];
        }

        return $data;
    }

    /**
     * Update email settings
     * @throws \PhpImap\Exception
     */
    public function settings_update()
    {
        $host = $this->input->post('host', true);
        $username = $this->input->post('username', true);
        $password = $this->input->post('password', true);
        $type = $this->input->post('type', true);
        $sslStatus = $this->input->post('ssl_status', true);
        $frequency = $this->input->post('frequency', true);
        $userId = $this->session->userdata('user_id');

        if ($this->imapCheckAccount($host, $username, $password, $sslStatus)) {
            $data = [
                'host'        => $host,
                'username'    => $username,
                'password'    => $password,
                'type'        => $type,
                'ssl_status'  => $sslStatus,
                'frequency'   => $frequency,
                'user_id'     => $userId,
                'port'        => $sslStatus == 0 ? 143 : 993,
                'next_update' => time() + ($frequency * 60)
            ];

            $row = $this->db->get_where('ip_email_settings', ['user_id' => $userId])->row_array();

            if ($row) {
                $this->db->where('user_id', $userId);
                $this->db->update('ip_email_settings', $data);
                $settingsId = $row['id'];
            } else {
                $this->db->insert('ip_email_settings', $data);
                $settingsId = $this->db->insert_id();
            }

            $this->imapUpdateListOfFolders($userId, $settingsId);
            $this->imapUpdateMessages($userId, $settingsId);

            success(lang('email_settings_updated'));
            redirect($this->agent->referrer() . '#tab_tab2');
        } else {
            $this->session->set_flashdata('alert_error', 'Error updating email settings');
            redirect($this->agent->referrer() . '#tab_tab2');
        }
    }

    /**
     * Get IMAP connection
     * @param $settingId
     * @param $folder
     * @return \PhpImap\Mailbox
     * @throws \PhpImap\Exception
     */
    private function getImapConnection($settingId, $folder)
    {
        $settings = $this->db->get_where(
            'ip_email_settings',
            ['id' => $settingId]
        )->row();

        $port = $settings->ssl_status == 0 ? 143 : 993;
        $ssl = $settings->ssl_status ? "/ssl" : null;

        return new PhpImap\Mailbox(
            "{{$settings->host}:{$port}/imap{$ssl}}{$folder}", $settings->username, $settings->password, sys_get_temp_dir()
        );
    }

    /**
     * Check imap account
     * @param $host
     * @param $username
     * @param $password
     * @param $sslStatus
     * @return bool
     * @throws \PhpImap\Exception
     */
    public function imapCheckAccount($host, $username, $password, $sslStatus)
    {
        $port = $sslStatus == 0 ? 143 : 993;
        $ssl = $sslStatus ? "/ssl" : null;

        $mailbox = new PhpImap\Mailbox("{{$host}:{$port}/imap{$ssl}}", $username, $password, sys_get_temp_dir());
        if (!$mailbox) {
            return false;
        }

        return true;
    }

    /**
     * Update list of boxes
     * @param $userId
     * @param $settingId
     * @return bool
     * @throws \PhpImap\Exception
     */
    public function imapUpdateListOfFolders($userId, $settingId)
    {
        $this->db->where('settings_id', $settingId);
        $this->db->set('deleted', 1);
        $this->db->update('ip_email_boxes');

        $folders = $this->imapGetListOfFolders($settingId);
        if (!$folders) {
            return false;
        }

        foreach ($folders as $folder) {
            $mailBoxId = $this->db->get_where('ip_email_boxes', ['settings_id' => $settingId, 'box_name' => $folder])->row()->id;
            $info = $this->getImapConnection($settingId, $folder)->getMailboxInfo();
            if ($info) {
                if ($mailBoxId) {
                    $this->db->where('settings_id', $settingId);
                    $this->db->where('box_name', $folder);
                    $this->db->set('nmsgs', $info->Nmsgs);
                    $this->db->set('unread', $info->Unread);
                    $this->db->set('size', $info->Size);
                    $this->db->set('deleted', 0);
                    $this->db->update('ip_email_boxes');
                } else {
                    $this->db->insert('ip_email_boxes', [
                        'user_id'     => $userId,
                        'settings_id' => $settingId,
                        'box_id'      => 0,
                        'box_name'    => $folder,
                        'nmsgs'       => $info->Nmsgs,
                        'recent'      => $info->Recent,
                        'unread'      => $info->Unread,
                        'deleted'     => $info->Deleted,
                        'size'        => $info->Size
                    ]);
                }
            }
        }
    }

    /**
     * Update messages
     * @param $userId
     * @param $settingId
     * @param null $criteria
     * @param bool $isNew
     * @param null $boxId
     * @return bool
     * @throws \PhpImap\Exception
     */
    public function imapUpdateMessages($userId, $settingId, $criteria = null, $isNew = true, $boxId = null)
    {
        $boxes = $this->db->get_where('ip_email_boxes', ['settings_id' => $settingId])->result();
        if (!$boxes) {
            return false;
        }

        if ($criteria == null && $isNew == false) {
            $criteria = 'SINCE "' . date('d-F-Y', time() - 3600 * 24 * 3 * 60) . '"';
        }

        foreach ($boxes as $box) {
            if ($box->nmsgs == 0) {
                continue;
            }

            if ($boxId && $box->id != $boxId) {
                continue;
            }

            $imap = $this->getImapConnection($settingId, $box->box_name);
            $messages = $imap->searchMailbox($criteria ?: 'ALL');

            if (!$messages || count($messages) == 0) {
                continue;
            }

            arsort($messages, SORT_DESC);

            $messagesInDatabase = [];

            $data = $this->db
                ->select('uid')
                ->from('ip_email_messages')
                ->where('settings_id', $settingId)
                ->where('user_id', $userId)
                ->get()
                ->result();

            if ($data) {
                foreach ($data as $d) {
                    $messagesInDatabase[$d->uid] = $d->uid;
                }
            }

            foreach ($messages as $id => $uid) {
                if (isset($messagesInDatabase[$uid])) {
                    unset($messages[$id]);
                }
            }

            if (count($messages) == 0) {
                continue;
            }

            $info = $imap->getMailsInfo($messages);
            $messagesInfo = [];
            if ($info) {
                foreach ($info as $i) {
                    $messagesInfo[$i->uid] = $i;
                }
            }

            $i = 0;
            foreach ($messages as $message) {
                if ($i > 25) {
                    continue;
                }

                $info = isset($messagesInfo[$message]) ? $messagesInfo[$message] : null;
                if (!$info) {
                    continue;
                }

                $emailMatches = null;
                preg_match('/(.*)\<(.*)\>/', $info->from, $emailMatches);
                if (!$emailMatches) {
                    continue;
                } else {
                    $fromName = trim($emailMatches[1]);
                    $fromEmail = $emailMatches[2];
                }

                $check = $this->db->get_where('ip_email_messages', [
                    'settings_id' => $settingId,
                    'uid'         => $info->uid
                ])->row();

                if ($check == false) {
                    $this->db->insert('ip_email_messages', [
                        'user_id'     => $userId,
                        'settings_id' => $settingId,
                        'box_id'      => $box->id,
                        'from_email'  => $fromEmail,
                        'from_name'   => $fromName ?: $fromEmail,
                        'subject'     => $info->subject,
                        'date'        => $info->date,
                        'uid'         => $info->uid,
                        'msg_no'      => $info->msgno,
                        'recent'      => $info->recent,
                        'flagged'     => $info->flagged,
                        'answered'    => $info->answered,
                        'deleted'     => $info->deleted,
                        'seen'        => $info->seen,
                        'size'        => $info->size,
                        'draft'       => $info->draft
                    ]);
                }

                $i++;
            }
        }

        $this->db->where('id', $settingId);
        $this->db->set('next_update', time() + 5 * 60);
        $this->db->update('ip_email_settings');
    }

    /**
     * Get list of folders
     * @param $settingId
     * @return array
     * @throws \PhpImap\Exception
     */
    public function imapGetListOfFolders($settingId)
    {
        $imap = $this->getImapConnection($settingId, null);
        $array = [];
        $mailboxes = $imap->getMailboxes('*');
        if (is_array($mailboxes) && count($mailboxes) > 0) {
            foreach ($mailboxes as $b) {
                if ($b['attributes'] == 34) {
                    continue;
                }

                if (preg_match('/^[\[\]]$/', $b) == false) {
                    if (stristr($b['shortpath'], '[gmail]') == false) {
                        array_push($array, $b['shortpath']);
                    }
                }
            }
        }

        return $array;
    }

    /**
     * Delete mail
     * @param $userId
     * @param $messageId
     */
    public function delete($userId, $messageId)
    {
        $settings = $this->db
            ->get_where('ip_email_settings', ['user_id' => $userId])
            ->row();

        if ($settings) {
            $mails = explode(',', $messageId);
            foreach ($mails as $m) {
                $message = $this->db->get_where('ip_email_messages', ['id' => $m])->row();
                if ($message->user_id == $userId) {
                    $mailBoxName = $this->db
                        ->get_where('ip_email_boxes', [
                            'box_id'      => $message->box_id,
                            'settings_id' => $settings->id
                        ])
                        ->row('box_name');

                    try {
                        $this->getImapConnection($settings->id, $mailBoxName)->deleteMail($message->uid);
                        $this->db->delete('ip_email_messages', ['id' => $message->id]);
                    } catch (Exception $e) {
                        continue;
                    }
                } else {
                    continue;
                }
            }

            echo json_encode([
                'status'   => 'success',
                'message'  => 'Message(s) deleted!',
                'box_id'   => $message->box_id
            ], JSON_UNESCAPED_UNICODE);
            die;
        } else {
            echo json_encode([
                'status'   => 'error',
                'message'  => lang('email_settings_not_found')
            ], JSON_UNESCAPED_UNICODE);
            die;
        }
    }

    /**
     * Sync email
     * @throws \PhpImap\Exception
     */
    public function syncEmailAccounts()
    {
        $settings = $this->db->get_where('ip_email_settings', ['next_update <' => time()])->result();

        foreach ($settings as $s) {
            $this->imapUpdateListOfFolders($s->user_id, $s->id);
            $this->imapUpdateMessages($s->user_id, $s->id, null, true);
        }

        echo 'Done';
        die;
    }

    public function email_notifications()
    {
        $settings = $this->db->get_where('ip_email_settings',
            ['user_id' => $this->session->userdata('user_id')])->row_array();

        if ($settings) {
            $this->db->where([
                'user_id' => $this->session->userdata('user_id'),
                'seen'    => '0'
            ]);
            $data = $this->db->count_all_results('ip_email_messages');
        } else {
            $data = 0;
        }

        return $data;
    }

    /**
     * Send email
     */
    public function send_email()
    {
        $settings = $this->db->get_where('ip_email_settings',
            ['user_id' => $this->session->userdata('user_id')]
        )->row_array();

        if ($settings) {
            $this->load->library('email', [
                'protocol'  => $this->config->item('smtp_protocol'),
                'smtp_host' => $this->config->item('smtp_host'),
                'smtp_port' => $this->config->item('smtp_port'),
                'smtp_user' => $this->config->item('smtp_user'),
                'smtp_pass' => $this->config->item('smtp_pass'),
                'mailtype'  => $this->config->item('smtp_mailtype')
            ]);

            $this->email->set_newline("\r\n");

            $to = explode(',', $this->input->post('email_to'));
            $cc = explode(',', $this->input->post('email_cc'));
            $bcc = explode(',', $this->input->post('email_bcc'));

            $this->load->library('upload', [
                'upload_path'   => 'uploads/attachment',
                'allowed_types' => '*',
                'max_size'      => 1000
            ]);

            if ($this->upload->do_upload('attach')) {
                $file = $this->upload->data('file_name');
                $this->email->attach('/home/admin/web/my.mdpcrm.com/public_html/uploads/attachment/' . $file);
                unlink('/home/admin/web/my.mdpcrm.com/public_html/uploads/attachment/' . $file);
            }

            $this->email->from($settings['username']);
            $this->email->to($to);
            $this->email->cc($cc);
            $this->email->bcc($bcc);

            $this->email->subject($this->input->post('subject'));
            $this->email->message($this->input->post('message'));

            if ($this->email->send()) {
                die(json_encode([
                    'status'   => 'success',
                    'message'  => lang('message_sent')
                ], JSON_UNESCAPED_UNICODE));
            } else {
                die(json_encode([
                    'status'   => 'error',
                    'message'  => $this->email->print_debugger()
                ], JSON_UNESCAPED_UNICODE));
            }
        } else {
            die(json_encode([
                'status'   => 'error',
                'message'  => lang('email_settings_not_found')
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    public function reply($id)
    {
        $total = explode(',', $id);

        foreach ($total as $t) {
            $data = $this->db->get_where('ip_email_messages', ['id' => $t])->row_array();

            if ($data['user_id'] == $this->session->userdata('user_id')) {
                if ($data['seen'] == 0) {
                    $settings = $this->db->get_where('ip_email_settings',
                        ['user_id' => $this->session->userdata('user_id')])->row_array();
                    $connection = imap_open('{' . $settings['host'] . ':' . $settings['port'] . '}',
                        $settings['username'], $settings['password']); // connect
                    imap_setflag_full($connection, $data['uid'], "\\Seen");
                }
                $dama[] = [
                    'email'   => $data['from_email'],
                    'subject' => $data['subject']
                ];
            } else {
                danger(lang('you_dont_have_access'));
                redirect('email');
                die;
            }
        }

        return array_unique($dama);
    }

    /**
     * Flag email
     * @param $userId
     * @param $messageId
     */
    public function flag($userId, $messageId)
    {
        $settings = $this->db->get_where('ip_email_settings', ['user_id' => $userId])->row_array();

        $messages = explode(',', $messageId);
        foreach ($messages as $id) {
            $m = $this->db->get_where('ip_email_messages', ['id' => $id])->row();
            if (!$m) {
                continue;
            }

            $boxName = $this->db->get_where('ip_email_boxes', ['id' => $m->box_id])->row('box_name');

            try {
                if ($m->flagged == 1) {
                    $this->db->where('id', $id);
                    $this->db->update('ip_email_messages', ['flagged' => 0]);
                    $this->getImapConnection($settings->id, $boxName)->clearFlag([$m->uid], "\Flagged");
                } else {
                    $this->db->where('id', $id);
                    $this->db->update('ip_email_messages', ['flagged' => 1]);
                    $this->getImapConnection($settings->id, $boxName)->setFlag([$m->uid], "\Flagged");
                }
            } catch (Exception $e) {
                continue;
            }
        }

        echo json_encode([
            'status' => 'success',
            'box_id' => $m->box_id
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Refresh email
     * @param null $boxId
     * @throws \PhpImap\Exception
     */
    public function refresh_email($boxId = null)
    {
        /* fetch all boxes */
        $settings = $this->db->get_where(
            'ip_email_settings', [
                'user_id' => $this->session->userdata('user_id')
            ])->row();

        if ($settings) {
            $box = $this->db->get_where('ip_email_boxes', ['user_id' => $settings->user_id])->row();
            if ($box) {
                $boxId = $box->id;
            } else {
                $boxId = -1;
            }

            if ($boxId == null) {
                $this->imapUpdateListOfFolders($settings->user_id, $settings->id);
            }

            $this->imapUpdateMessages($settings->user_id, $settings->id, null, false);

            $this->db->where('id', $settings->id);
            $this->db->update('ip_email_settings', [
                'next_update' => time() + $settings->frequency * 60
            ]);

            echo json_encode([
                'status'   => 'success',
                'message'  => lang('mail_box_refreshed')
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'status'   => 'error',
                'message'  => lang('email_settings_not_found')
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Get list of boxes
     * @param $userId
     * @return array
     */
    public function boxes($userId)
    {
        $data = [];
        $boxes = $this->db
            ->get_where('ip_email_boxes', ['user_id' => $userId])
            ->result();

        if ($boxes) {
            foreach ($boxes as $b) {
                if ($b->box_name == 'Inbox') {
                    $data[] = $b;
                }
            }

            foreach ($boxes as $b) {
                if ($b->box_name != 'Inbox') {
                    $data[] = $b;
                }
            }
        }

        return $data;
    }

    /**
     * Notifications, dunno where this is used
     * @param $id
     */
    public function notifications($id)
    {
        if (
            $this->db->get_where('ip_notifications', [
                'user_id' => $this->session->userdata('user_id'),
                'notification_id' => $id
            ])->num_rows() == 0
        ) {
            $this->db->insert('ip_notifications', [
                'user_id'         => $this->session->userdata('user_id'),
                'notification_id' => $id
            ]);
        }
    }

    public function clients_inbox($clientId)
    {
        $fromEmail = $this->db->get_where('ip_clients', ['client_id' => $clientId])->row('client_email');

        if ($fromEmail) {
            $boxRow = $this->db->get_where('ip_email_boxes', [
                'user_id' => $this->session->userdata('user_id'),
                'box_name' => 'Inbox'
            ])->row_array();

            $this->db->order_by('date', 'desc');
            $this->db->limit(25);
            $messages = $this->db->get_where('ip_email_messages', [
                'user_id'    => $boxRow['user_id'],
                'box_id'     => $boxRow['id'],
                'from_email' => $fromEmail
            ])->result_array();

            if ($messages) {
                foreach ($messages as $row) {
                    if ($row['star'] == 0) {
                        $star = '-o';
                        $flag_status = 'flag-m';
                    } else {
                        $star = '';
                        $flag_status = 'unflag';
                    }

                    if ($row['flagged'] == 0) {
                        $flagged = '';
                    } else {
                        $flagged = '<i class="fa fa-flag"></i>';
                    }

                    if ($row['attached'] == 0) {
                        $attached = '';
                    } else {
                        $attached = '<i class="fa fa-paperclip"></i>';
                    }

                    if ($row['seen'] == 0) {
                        $start = '<b>';
                        $end = '</b>';
                    } else {
                        $start = '';
                        $end = '';
                    }

                    $data[] = '<tr>
                        <td ><a style="text-decoration: none !important; color: #000 !important" href="' . '/email/client_details/' . $row['id'] . '" class="details">' . $start . $row['subject'] . $end . '</a></td>
                        <td class="inbox-small-cells"><i class="fa fa-star' . $star . ' ' . $flag_status . '" data-id="' . $row['id'] . '"></i></td>
                        <td class="view-message">' . $start . date('d/m/Y H:i', $row['date']) . $end . '</td>
                        <td class="view-message">' . $start . number_format($row['size']) . ' KB ' . $end . '</td>
                        <td class="inbox-small-cells">' . $flagged . '</td>
                        <td class="inbox-small-cells">' . $attached . '</td>
                    </tr>';
                }
            } else {
                $data[] = '<tr><td class="text-center" colspan="6">' . lang('there_is_no_emails_yet') . '</td></tr>';
            }
        } else {
            $data[] = '<tr><td class="text-center" colspan="6">' . lang('user_has_no_mail_address') . '</td></tr>';
        }

        return $data;
    }

    public function clients_outbox($clientId)
    {
        $fromEmail = $this->db->get_where('ip_clients', ['client_id' => $clientId])->row('client_email');

        if ($fromEmail) {
            $boxRow = $this->db->get_where('ip_email_boxes', [
                'user_id' => $this->session->userdata('user_id'),
                'box_name' => 'Sent Items'
            ])->row_array();

            $this->db->order_by('date', 'desc');
            $this->db->limit(25);
            $messages = $this->db->get_where('ip_email_messages', [
                'user_id'    => $boxRow['user_id'],
                'box_id'     => $boxRow['id'],
                'from_email' => $fromEmail
            ])->result_array();

            if ($messages) {
                foreach ($messages as $row) {
                    if ($row['star'] == 0) {
                        $star = '-o';
                        $flag_status = 'flag-m';
                    } else {
                        $star = '';
                        $flag_status = 'unflag';
                    }

                    if ($row['flagged'] == 0) {
                        $flagged = '';
                    } else {
                        $flagged = '<i class="fa fa-flag"></i>';
                    }

                    if ($row['attached'] == 0) {
                        $attached = '';
                    } else {
                        $attached = '<i class="fa fa-paperclip"></i>';
                    }

                    if ($row['seen'] == 0) {
                        $start = '<b>';
                        $end = '</b>';
                    } else {
                        $start = '';
                        $end = '';
                    }

                    $data[] = '<tr>
                        <td ><a style="text-decoration: none !important; color: #000 !important" href="' . site_url('email/client_details/' . $row['id']) . '" class="details">' . $start . $row['subject'] . $end . '</a></td>
                        <td class="inbox-small-cells"><i class="fa fa-star' . $star . ' ' . $flag_status . '" data-id="' . $row['id'] . '"></i></td>
                        <td class="view-message">' . $start . date('d/m/Y H:i', $row['date']) . $end . '</td>
                        <td class="view-message">' . $start . number_format($row['size']) . ' KB ' . $end . '</td>
                        <td class="inbox-small-cells">' . $flagged . '</td>
                        <td class="inbox-small-cells">' . $attached . '</td>
                    </tr>';
                }
            } else {
                $data[] = '<tr><td class="text-center" colspan="6">' . lang('there_is_no_emails_yet') . '</td></tr>';
            }
        } else {
            $data[] = '<tr><td class="text-center" colspan="6">' . lang('user_has_no_mail_address') . '</td></tr>';
        }

        return $data;
    }

    /**
     * Client details
     * @param $id
     * @return array
     * @throws \PhpImap\Exception
     */
    public function client_details($id)
    {
        $settings = $this->db->get_where(
            'ip_email_settings',
            ['user_id' => $this->session->userdata('user_id')]
        )->row();

        $data = $this->db->get_where('ip_email_messages', ['id' => $id])->row_array();

        if ($data['user_id'] != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', lang('you_dont_have_access'));
            redirect($this->agent->referrer());
        }

        if ($data['seen'] == 0) {
            $this->getImapConnection($settings->id, null)->setFlag([$data['uid']], '\Seen');
            $this->db->where('id', $data['id']);
            $this->db->update('ip_email_messages', ['seen' => 1]);
        }

        $contentData = $this->db->get_where('ip_email_messages_body', ['email_message_id' => $id])->row();
        if ($contentData) {
            $data['content'] = $contentData->content;
            $data['content_html'] = $contentData->is_html;
        } else {
            $data['content'] = 'No body for this email';
            $data['content_html'] = 0;
        }

        $client_id = $this->db->get_where('ip_clients', [
            'company_id'   => $this->session->userdata('company_id'),
            'client_email' => $data['from_email']
        ])->row('client_id');

        $data = [
            'from_email' => $data['from_email'],
            'date'       => date('d/m/Y H:i', strtotime($data['date'])),
            'subject'    => $data['subject'],
            'content'    => $data['content'],
            'id'         => $data['id'],
            'client_id'  => $client_id
        ];

        return $data;
    }

    /**
     * Set email flag
     * @param $settingsId
     * @param $id
     * @param $uid
     * @param $flag
     * @throws \PhpImap\Exception
     */
    public function set_email_flag($settingsId, $id, $uid, $flag)
    {
        $this->getImapConnection($settingsId, null)->setFlag([$uid], $flag);
        $this->db->where('id', $id);
        $this->db->update('ip_email_messages', ['seen' => 1]);
    }

    /**
     * Get email body
     * @param $settingsId
     * @param $boxName
     * @param $uid
     * @return \PhpImap\IncomingMail
     * @throws \PhpImap\Exception
     */
    public function get_email_body($settingsId, $boxName, $uid)
    {
        return $this->getImapConnection($settingsId, $boxName)->getMail($uid, false);
    }
}
