<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_sessions extends CI_Model
{
    /**
     * Authorize
     * @param $email
     * @param $password
     * @return bool
     */
    public function auth($email, $password)
    {
        $this->db->where('user_email', $email);
        $query = $this->db->get('ip_users');

        if ($query->num_rows()) {
            $user = $query->row();
            if (!$user->user_psalt) {
                if (md5($password) == $user->user_password) {
                    /**
                     * The md5 login validated - let's update this user
                     * to the new hash
                     */
                    $salt = $this->crypt->salt();
                    $hash = $this->crypt->generate_password($password, $salt);

                    $db_array = [
                        'user_psalt'    => $salt,
                        'user_password' => $hash
                    ];

                    $this->db->where('user_id', $user->user_id);
                    $this->db->update('ip_users', $db_array);

                    $this->db->where('user_email', $email);
                    $user = $this->db->get('ip_users')->row();

                } else {
                    return false;
                }
            }

            if ($this->crypt->check_password($user->user_password, $password)) {
                switch ($user->user_type) {
                    case '3':
                        $userType = 'admin';
                        break;
                    case '1':
                        $userType = 'User';
                        break;
                    default:
                        $userType = '';
                        break;
                }

                $this->session->set_userdata([
                    'user_type'      => $user->user_type,
                    'user_type_name' => $userType,
                    'user_id'        => $user->user_id,
                    'user_name'      => $user->user_name,
                    'company_name'   => $user->user_company,
                    'company_id'     => $user->company_id
                ]);

                return true;
            }
        }

        return false;
    }
}
