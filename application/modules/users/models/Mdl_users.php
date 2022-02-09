<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_users extends Response_Model
{
    const TYPE_GUEST = 2;
    const TYPE_ADMIN = 1;
    const TYPE_ADMIN_COMPANY = 3;
    const TYPE_MASTER = 0;

    public $table = 'ip_users';
    public $primary_key = 'ip_users.user_id';
    public $date_created_field = 'user_date_created';
    public $date_modified_field = 'user_date_modified';

    /**
     * User types
     * @return array
     */
    public function user_types()
    {
        return [
            self::TYPE_GUEST         => lang('guest_read_only'), //guest - readd only
            self::TYPE_ADMIN         => lang('administrator'), // normal user
            self::TYPE_ADMIN_COMPANY => lang('company_admin'),
            self::TYPE_MASTER        => lang('master_admin'),
        ];
    }

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('
            SQL_CALC_FOUND_ROWS
            ip_user_custom.*,
            ip_users.*,
            ip_companies.company_name AS company_name',
            false
        );
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_user_custom', 'ip_user_custom.user_id = ip_users.user_id', 'left');
        $this->db->join('ip_companies', 'ip_companies.company_id = ip_users.company_id', 'left');
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_companies.company_name ASC,ip_users.user_name ASC');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'user_type'      => [
                'field' => 'user_type',
                'label' => lang('user_type'),
                'rules' => 'required'
            ],
            'user_email'     => [
                'field' => 'user_email',
                'label' => lang('email'),
                'rules' => 'required|valid_email|is_unique[ip_users.user_email]'
            ],
            'user_name'      => [
                'field' => 'user_name',
                'label' => lang('name'),
                'rules' => 'required'
            ],
            'user_password'  => [
                'field' => 'user_password',
                'label' => lang('password'),
                'rules' => 'required|min_length[8]'
            ],
            'user_passwordv' => [
                'field' => 'user_passwordv',
                'label' => lang('verify_password'),
                'rules' => 'required|matches[user_password]'
            ],
            'user_company'   => [
                'field' => 'user_company'
            ],
            'company_id'     => [
                'field' => 'company_id',
                'label' => lang('company'),
                'rules' => 'required'
            ],
            'user_address_1' => ['field' => 'user_address_1'],
            'user_address_2' => ['field' => 'user_address_2'],
            'user_city'      => ['field' => 'user_city'],
            'user_state'     => ['field' => 'user_state'],
            'user_zip'       => ['field' => 'user_zip'],
            'user_country'   => [
                'field' => 'user_country',
                'label' => lang('country'),
            ],
            'user_phone'     => ['field' => 'user_phone'],
            'user_fax'       => ['field' => 'user_fax'],
            'user_mobile'    => ['field' => 'user_mobile'],
            'user_web'       => ['field' => 'user_web'],
            'user_vat_id'    => ['field' => 'user_vat_id'],
            'user_tax_code'  => ['field' => 'user_tax_code']
        ];
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules_existing()
    {
        return [
            'user_type'      => [
                'field' => 'user_type',
                'label' => lang('user_type'),
                'rules' => 'required'
            ],
            'user_email'     => [
                'field' => 'user_email',
                'label' => lang('email'),
                'rules' => 'required|valid_email'
            ],
            'user_name'      => [
                'field' => 'user_name',
                'label' => lang('name'),
                'rules' => 'required'
            ],
            'user_company'   => [
                'field' => 'user_company'
            ],
            'company_id'     => [
                'field' => 'company_id',
                'label' => lang('company'),
                'rules' => 'required'
            ],
            'user_address_1' => [
                'field' => 'user_address_1'
            ],
            'user_address_2' => [
                'field' => 'user_address_2'
            ],
            'user_city'      => [
                'field' => 'user_city'
            ],
            'user_state'     => [
                'field' => 'user_state'
            ],
            'user_zip'       => [
                'field' => 'user_zip'
            ],
            'user_country'   => [
                'field' => 'user_country',
                'label' => lang('country'),
                'rules' => 'required'
            ],
            'user_phone'     => [
                'field' => 'user_phone'
            ],
            'user_fax'       => [
                'field' => 'user_fax'
            ],
            'user_mobile'    => [
                'field' => 'user_mobile'
            ],
            'user_web'       => [
                'field' => 'user_web'
            ],
            'user_vat_id'    => [
                'field' => 'user_vat_id'
            ],
            'user_tax_code'  => [
                'field' => 'user_tax_code'
            ]
        ];
    }

    /**
     * Validation rules for changing password
     * @return array
     */
    public function validation_rules_change_password()
    {
        return [
            'user_password'  => [
                'field' => 'user_password',
                'label' => lang('password'),
                'rules' => 'required'
            ],
            'user_passwordv' => [
                'field' => 'user_passwordv',
                'label' => lang('verify_password'),
                'rules' => 'required|matches[user_password]'
            ]
        ];
    }

    /**
     * Update token
     * @param $userId
     * @param $token
     */
    public function updateToken($userId, $token)
    {
        $this->db->update($this->table, ['user_passwordreset_token' => $token], ['user_id' => $userId]);
    }

    /**
     * Change password
     * @param $userId
     * @param $password
     */
    public function changePassword($userId, $password)
    {
        $salt = $this->crypt->salt();
        $userPassword = $this->crypt->generate_password($password, $salt);

        $this->db->update(
            $this->table,
            ['user_psalt' => $salt, 'user_password' => $userPassword],
            ['user_id' => $userId]
        );
    }

    /**
     * DB array
     * @return array
     */
    public function db_array()
    {
        $data = parent::db_array();

        if (isset($data['user_password'])) {
            unset($data['user_passwordv']);

            $user_psalt = $this->crypt->salt();

            $data['user_psalt'] = $user_psalt;
            $data['user_password'] = $this->crypt->generate_password($data['user_password'], $user_psalt);
        }

        return $data;
    }

    /**
     * Set flash
     * @param null $id
     * @param null $data
     * @param bool $setFlash
     * @return null
     */
    public function save($id = null, $data = null, $setFlash = true)
    {
        $id = parent::save($id, $data, $setFlash);

        if ($clients = $this->session->userdata('user_clients')) {
            $this->load->model('Mdl_user_clients');

            foreach ($clients as $client) {
                $this->Mdl_user_clients->save(null, [
                    'user_id'   => $id,
                    'client_id' => $client
                ]);
            }

            $this->session->unset_userdata('user_clients');
        }

        return $id;
    }

    /**
     * TODO: remove orphan and remove this
     * @param $id
     */
    public function delete($id)
    {
        parent::delete($id);

        delete_orphans();
    }

    public function settings_update()
    {
        $host = $this->input->post('host', true);
        $username = $this->input->post('username', true);
        $password = $this->input->post('password', true);
        $type = $this->input->post('type', true);
        $ssl_status = $this->input->post('ssl_status', true);
        $frequency = $this->input->post('frequency', true);
        $user_id = $this->input->post('user_id');

        /* define port number */
        if ($type == 0) {
            if ($ssl_status == 0) {
                $port = '143';
            } else {
                $port = '993';
            }
        } else {
            if ($ssl_status == 0) {
                $port = '110';
            } else {
                $port = '995';
            }
        }

        $connection = imap_open('{' . $host . ':' . $port . '}', $username, $password); // connect
        $check = imap_check($connection); // check connection

        if ($check) {
            /* insert or update email settings */
            $data = [
                'host'        => $host,
                'username'    => $username,
                'password'    => $password,
                'type'        => $type,
                'ssl_status'  => $ssl_status,
                'frequency'   => $frequency,
                'user_id'     => $user_id,
                'port'        => $port,
                'next_update' => time() + ($frequency * 60)
            ];

            /* check if settings row exists */
            $row = $this->db->get_where('ip_email_settings', ['user_id' => $user_id])->row_array();

            if ($row) {
                $this->db->where('user_id', $user_id);
                $this->db->update('ip_email_settings', $data);
            } else {
                $this->db->insert('ip_email_settings', $data);
                $settings_id = $this->db->insert_id();

                /* check boxes */
                $list = imap_list($connection, '{' . $host . '}', "*");
                imap_close($connection); //close connection

                foreach ($list as $li) {
                    $key = array_search($li, $list);
                    $name = str_replace('{' . $host . '}', '', $li);

                    $checkDetailsConnection = imap_open("\{$host:$port\}$name", $username, $password);
                    $box_details = imap_mailboxmsginfo($checkDetailsConnection);
                    imap_close($checkDetailsConnection); //close connection

                    $this->db->insert('ip_email_boxes', [
                        'user_id'     => $user_id,
                        'settings_id' => $settings_id,
                        'box_id'      => $key,
                        'box_name'    => $name,
                        'nmsgs'       => $box_details->Nmsgs,
                        'recent'      => $box_details->Recent,
                        'unread'      => $box_details->Unread,
                        'deleted'     => $box_details->Deleted,
                        'size'        => $box_details->Size
                    ]);
                }
            }

            success(lang('email_settings_updated'));
            redirect($this->agent->referrer() . '#tab_tab2');
        } else {
            error(imap_last_error());
            redirect($this->agent->referrer() . '#tab_tab2');
        }
    }

    /**
     * Get company url from name
     * @param $companyName
     * @return null|string|string[]
     */
    public function generateCompanyUrl($companyName)
    {
        if (strlen($companyName) > 0) {
            $companyName = strtolower($companyName);
            $companyName = str_replace('uab', '', $companyName);
            $companyName = str_replace(' ', '-', $companyName);
            $companyName = preg_replace('/[^a-z0-9\-]/', '', $companyName);
            $companyName = preg_replace('/\-+/', '-', $companyName);

            return $companyName;
        }

        return null;
    }

    /**
     * Get company url by user id
     * @param $userId
     * @return null
     */
    public function getCompanyUrlByUserId($userId)
    {
        $row = $this->db->get_where($this->table, ['user_id' => $userId])->row();
        if ($row) {
            return $row->user_company_url;
        }

        return null;
    }

    /**
     * Get company id by url
     * @param $companyUrl
     * @return null
     */
    public function getCompanyIdByUrl($companyUrl)
    {
        $row = $this->db->get_where($this->table, ['user_company_url' => $companyUrl])->row();
        if ($row) {
            return $row->company_id;
        }

        return null;
    }
}
