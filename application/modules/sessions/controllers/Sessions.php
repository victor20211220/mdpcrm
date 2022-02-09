<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sessions extends Base_Controller
{
    /**
     * Sessions constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_settings',
            'Mdl_sessions',
            'users/Mdl_users'
        ]);
    }

    /**
     * Index controller
     */
    public function index()
    {
        redirect('sessions/login');
    }

    /**
     * Login page
     */
    public function login()
    {
        $view_data = [
            'login_logo' => $this->Mdl_settings->setting('login_logo')
        ];

        if ($this->input->post('btn_register', true)) {
            redirect('register/company');
        }

        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        }

        if ($this->input->post('btn_login', true)) {
            $this->db->where('user_email', $this->input->post('email', true));
            $query = $this->db->get('ip_users');
            $user = $query->row();

            if ($user) {
                if ($this->authenticate($this->input->post('email', true), $this->input->post('password', true))) {
                    if ($this->session->userdata('user_type') == 1 || //normal user
                        $this->session->userdata('user_type') == 0 || //master admin
                        $this->session->userdata('user_type') == 3    //company admin
                    ) {
                        redirect('dashboard');
                    } elseif ($this->session->userdata('user_type') == 2) {
                        redirect('guest');
                    }
                } else {
                    $this->session->set_flashdata('alert_error', lang('loginalert_credentials_incorrect'));
                }
            }

            if (empty($user)) {
                $this->session->set_flashdata('alert_error', lang('loginalert_user_not_found'));
            }

            if ($this->input->post('btn_login', true) && empty($user) == false && $user->user_active == 0) {
                $this->session->set_flashdata('alert_error', lang('loginalert_user_inactive'));
            }
        }

        $this->load->view('session_login', $view_data);
    }

    /**
     * Authenticate
     * @param $email
     * @param $password
     * @return bool
     */
    public function authenticate($email, $password)
    {
        return $this->Mdl_sessions->auth($email, $password) ? true : false;
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('sessions/login');
    }

    /**
     * Password reset
     * @param null $token
     * @return mixed
     */
    public function passwordreset($token = null)
    {
        if ($token) {
            $this->db->where('user_passwordreset_token', $token);
            $user = $this->db->get('ip_users');
            $user = $user->row();

            if (empty($user)) {
                $this->session->set_flashdata('alert_success', lang('wrong_passwordreset_token'));
                redirect('sessions/login');
            }

            return $this->load->view('session_new_password', [
                'user_id' => $user->user_id
            ]);
        }
        // Check if the form for a new password was used
        if ($this->input->post('btn_new_password', true)) {
            $newPassword = $this->input->post('new_password', true);
            $userId = $this->input->post('user_id', true);

            if (empty($userId) || empty($newPassword)) {
                $this->session->set_flashdata('alert_error', lang('loginalert_no_password'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            $this->Mdl_users->changePassword($userId, $newPassword);
            $this->Mdl_users->updateToken($userId, '');
            $this->session->set_flashdata('alert_success', 'Password Successfully Changed');

            redirect('sessions/login');
        }

        // Check if the password reset form was used
        if ($this->input->post('btn_reset', true)) {
            $email = $this->input->post('email', true);
            if (empty($email)) {
                $this->session->set_flashdata('alert_error', lang('loginalert_user_not_found'));
                redirect($_SERVER['HTTP_REFERER']);
            }
            // Test if a user with this email exists
            $userData = $this->db->get_where('ip_users', ['user_email' => $email])->row();
            if ($userData) {
                $token = md5(time() . $email);
                $this->Mdl_users->updateToken($userData->user_id, $token);

                $emailResetLink = base_url() . 'sessions/passwordreset/' . $token;
                $emailMessage = $this->load->view('emails/passwordreset', ['resetlink' => $emailResetLink], true);
                $emailFrom = 'no-reply@' . preg_replace("/^[\w]{2,6}:\/\/([\w\d\.\-]+).*$/", "$1", base_url());

                $config = [
                    'protocol'  => $this->config->item('smtp_protocol'),
                    'smtp_host' => $this->config->item('smtp_host'),
                    'smtp_port' => $this->config->item('smtp_port'),
                    'smtp_user' => $this->config->item('smtp_user'),
                    'smtp_pass' => $this->config->item('smtp_pass'),
                    'mailtype'  => $this->config->item('smtp_mailtype')
                ];

                $this->load->library('email', $config);
                $this->email->set_newline("\r\n");
                $this->email->from($emailFrom);
                $this->email->to($email);
                $this->email->subject(lang('password_reset'));
                $this->email->message($emailMessage);

                if ($this->email->send()) {
                    $this->session->set_flashdata('alert_success', lang('email_successfully_sent'));
                    redirect('sessions/login');
                } else {
                    $this->session->set_flashdata('alert_error', lang('error'));
                }
            } else {
                $this->session->set_flashdata('alert_error', lang('loginalert_user_not_found'));
            }
        }

        return $this->load->view('session_passwordreset');
    }
}
