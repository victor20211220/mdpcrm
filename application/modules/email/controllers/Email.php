<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email extends Admin_Controller
{
    /**
     * Email constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('email/Mdl_email');
        $this->load->library('form_validation');
    }

    /**
     * Index action
     */
    public function index()
    {
        $this->load->view('email/index');
    }

    /**
     * Layout action
     */
    public function layout()
    {
        $this->load->view('email/layout', [
            'boxesMenu' => $this->boxes(true, true),
            'boxesDrop' => $this->boxes(false, true),
            'mails'     => $this->box_emails(null, true)
        ]);
    }

    /**
     * Show mailboxes
     * @param $forMenu
     * @param $return
     * @return mixed
     */
    public function boxes($forMenu, $return)
    {
        $data = $this->Mdl_email->boxes($this->userId);

        if ($return === true) {
            return $this->load->view('boxes', [
                'boxes'   => $data,
                'forMenu' => $forMenu
            ], true);
        } else {
            $this->load->view('boxes', [
                'boxes'   => $data,
                'forMenu' => $forMenu
            ]);
        }
    }

    /**
     * Box details
     * @param $boxId
     * @param bool $return
     * @return mixed
     */
    public function box_emails($boxId, $return = false)
    {
        if ($boxId == null) {
            $boxData = $this->db->get_where('ip_email_boxes', [
                'user_id' => $this->userId,
                'box_name' => 'Inbox'
            ])->row();
        } else {
            $boxData = $this->db->get_where('ip_email_boxes', [
                'user_id' => $this->userId,
                'box_name' => 'Inbox'
            ])->row();
        }

        if ($boxData) {
            $boxId = $boxData->id;
            $boxName = $boxData->box_name;
        } else {
            $boxId = 0;
            $boxName = 'All mailboxes';
        }

        $box = $this->db->get_where('ip_email_boxes', ['id' => $boxId])->row();

        if ($boxId) {
            $this->db->order_by('uid', 'DESC');
            $messages = $this->db->get_where(
                'ip_email_messages', [
                'user_id' => $box->user_id,
                'box_id'  => $box->id
            ])->result();
        } else {
            $messages = null;
        }

        if ($return === true) {
            return $this->load->view('box_emails', [
                'isSearch' => false,
                'messages' => $messages,
                'boxId'    => $boxId,
                'boxName'  => $boxName
            ], true);
        } else {
            $this->load->view('box_emails', [
                'isSearch' => false,
                'messages' => $messages,
                'boxId'    => $boxId,
                'boxName'  => $boxName
            ]);
        }
    }

    /**
     * Search
     */
    public function search()
    {
        $boxId = $this->input->post('box_id');
        $text = $this->input->post('text');

        $this->db->order_by('date', 'desc');
        $this->db->select('ip_email_messages.*');
        $this->db->from('ip_email_messages');
        $this->db->join('ip_email_messages_body', 'email_message_id = ip_email_messages.id', 'left');
        $this->db->where("
            (
                subject LIKE '%{$text}%' OR
                content LIKE '%{$text}%' OR
                from_name LIKE '%{$text}%' OR
                from_email LIKE '%{$text}%'
            )
        ");
        $this->db->where('user_id', $this->userId);
        if ($boxId) {
            $this->db->where('box_id', $boxId);
        }

        $data = $this->db->get()->result();

        $this->load->view('box_emails', [
            'isSearch' => true,
            'messages' => $data,
            'boxId'    => 0,
            'boxName'  => 'All mailboxes'
        ]);
    }

    /**
     * Compose
     */
    public function compose()
    {
        $messageId = $this->input->post('message_id');
        $action = $this->input->post('action');
        $mailTo = null;
        $subject = null;
        $mailBody = null;

        $isForward = $action == 'forward';
        $isReply = $action == 'reply';

        if ($messageId) {
            $data = $this->db->get_where('ip_email_messages', ['id' => $messageId])->row();
            $mailBody = $this->db->get_where('ip_email_messages_body', ['email_message_id' => $messageId])->row();
            if ($data && $action == 'reply') {
                $mailTo = $data->from_email;
                $subject = "Re: {$data->subject}";
            } elseif ($data && $action == 'forward') {
                $mailTo = null;
                $subject = "Fwd: {$data->subject}";
            }
        }

        $this->load->view('email/compose', [
            'mailTo'    => $mailTo,
            'subject'   => $subject,
            'isForward' => $isForward,
            'isReply'   => $isReply,
            'mailBody'  => $messageId ? $mailBody : null
        ]);
    }

    /**
     * Get email body
     * @param $id
     */
    public function body($id)
    {
        echo $this->db->get_where('ip_email_messages_body', ['email_message_id' => $id])->row('content');
    }

    /**
     * Update settings
     */
    public function settings_update()
    {
        $this->form_validation->set_rules('host', lang('email_host'), 'required');
        $this->form_validation->set_rules('username', lang('email_username', 'required'));
        $this->form_validation->set_rules('password', lang('email_password'), 'required');
        $this->form_validation->set_rules('type', lang('email_type'), 'required|in_list[0,1]');
        $this->form_validation->set_rules('ssl_status', lang('email_ssl'), 'required|in_list[0,1]');
        $this->form_validation->set_rules('frequency', lang('email_frequency'), 'required|in_list[5,10,15,30,60]');

        if ($this->form_validation->run() === false) {
            $this->settings();
        } else {
            $this->Mdl_email->settings_update();
        }
    }

    /**
     * Settings page
     */
    public function settings()
    {
        $data['settings'] = $this->Mdl_email->settings();
        $this->load->view('email/settings', $data);
    }

    /**
     * Send email
     */
    public function send_email()
    {
        $this->form_validation->set_rules('email_to', lang('email_receipent'), 'required|valid_email');
        $this->form_validation->set_rules('subject', lang('email_subject'), 'required');
        $this->form_validation->set_rules('message', lang('email_content'), 'required');

        if ($this->form_validation->run() === false) {
            $errorsArray = explode("\n", validation_errors());
            $error = preg_replace("/\<\/*p\>/", "",  $errorsArray[0]);
            echo json_encode([
                'status'  => 'error',
                'message' => $error
            ], JSON_UNESCAPED_UNICODE);
        } else {
            $this->Mdl_email->send_email();
        }
    }

    /**
     * Sync email
     */
    public function sync_email()
    {
        $this->Mdl_email->sync_email();
    }

    /**
     * Flag
     * @param $id
     */
    public function flag($id)
    {
        $this->Mdl_email->flag($this->userId, $id);
    }

    /**
     * Delete mail
     * @param $id
     */
    public function delete($id)
    {
        $this->Mdl_email->delete($this->userId, $id);
    }

    /**
     * Star email
     * @param $id
     */
    public function star_email($id)
    {
        $this->Mdl_email->star_email($this->userId, $id);
    }

    /**
     * Details
     * @param $id
     */
    public function details($id)
    {
        $settings = $this->db->get_where('ip_email_settings', ['user_id' => $this->userId])->row();
        $data = $this->db->get_where('ip_email_messages', ['id' => $id])->row();
        $contentData = $this->db->get_where('ip_email_messages_body', ['email_message_id' => $id])->row();

        if ($data->user_id == $this->userId) {
            if ($data->seen == 0) {
                $this->Mdl_email->set_email_flag($settings->id, $data->id, $data->uid, '\Seen');
            }

            $boxName = $this->db->get_where('ip_email_boxes', ['id' => $data->box_id])->row('box_name');
            if (!$contentData) {
                $content = $this->Mdl_email->get_email_body($settings->id, $boxName, $data->uid);

                if ($content) {
                    $isHtml = empty($content->textHtml) ? 0 : 1;
                    $data->content = $content->textHtml ? $content->textHtml : nl2br($content->textPlain);
                    $data->content_html = $isHtml;
                    $this->db->insert('ip_email_messages_body', [
                        'email_message_id' => $id,
                        'is_html'          => $isHtml,
                        'content'          => $data->content
                    ]);
                }
            } else {
                $data->content = $contentData->content;
                $data->content_html = $contentData->is_html;
            }

            $this->load->view('details', [
                'id'   => $id,
                'data' => $data
            ]);
        }
    }

    /**
     * Reply body
     * @param $id
     */
    public function reply_body($id)
    {
        $this->Mdl_email->reply_body($id);
    }

    /**
     * Refrash email
     */
    public function refresh_email($boxId)
    {
        $boxId = intval($boxId);
        $this->Mdl_email->refresh_email($boxId);
    }

    /**
     * Notifications
     * @param $id
     */
    public function notifications($id)
    {
        $this->Mdl_email->notifications($id);
    }

    /**
     * Client details
     * @param $id
     */
    public function client_details($id)
    {
        $this->layout
            ->set(['data' => $this->Mdl_email->client_details($id)])
            ->buffer('content', 'email/client_details')
            ->render();
    }
}
