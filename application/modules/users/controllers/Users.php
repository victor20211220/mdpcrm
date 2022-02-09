<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends Admin_Controller
{
    /**
     * Users constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_users',
            'Mdl_access_resources',
            'Mdl_users_access_resources',
            'Mdl_user_clients',
            'Mdl_clients',
            'Mdl_companies',
            'Mdl_custom_fields',
            'Mdl_custom_fields_data',
            'email/Mdl_email'
        ]);

        $this->load->library('form_validation');
    }

    /**
     * Index action
     * @param int $page
     */
    public function index($page = 0)
    {
        switch ($this->userType) {
            case 0 :
                break;
            case 3 :
                $this->Mdl_users->filter_where('ip_users.company_id', $this->companyId);
                $this->Mdl_users->where('ip_users.user_type !=', 0);
                break;
            case 1 :
                $this->Mdl_users->filter_where('ip_users.company_id', $this->companyId);
                $this->Mdl_users->where('ip_users.user_type !=', 0);
                break;
        }

        $this->Mdl_users->paginate(site_url('users/index'), $page);
        $users = $this->Mdl_users->result();

        $this->layout
            ->set('users', $users)
            ->set('my_user_type', $this->userType)
            ->set('my_user_id', $this->userId)
            ->set('user_types', $this->Mdl_users->user_types())
            ->buffer('content', 'users/index')
            ->render();
        ;
    }

    /**
     * Form
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel', true)) {
            redirect('users');
        }

        $accessResources = $this->Mdl_access_resources->get_all();
        $preloadedAccessResources = null;
        if (isset($id)) {
            $params = [$id];
            $preloadedAccessResources = $this->Mdl_users_access_resources->get_resources_for_user($params);
        }

        switch ($this->userType) {
            case 0:
                break;
            case 3 :
                //company admin
                if ($id && $this->Mdl_users->getByPk($id)->company_id != $this->companyId) {
                    show_404();
                }
                break;
            case 1 :
                //normal user
                if (!$id) {
                    show_404();
                }

                if ($this->Mdl_users->getByPk($id)->user_id != $this->userId) {
                    show_404();
                }
                break;
        }

        if ($_POST) {
            $_POST['company_id'] = $this->companyId;
        }

        if ($this->Mdl_users->run_validation(($id) ? 'validation_rules_existing' : 'validation_rules')) {
            $id = $this->Mdl_users->save($id);

            $this->Mdl_custom_fields_data->save_custom($id, $this->input->post('custom', true), 'ip_user_custom');

            if (isset($_POST['btn_submit'])) {
                if ($_POST['user_type'] == 3) {
                    for ($i = 0; $i < 10; $i++) {
                        $_POST['access_resources'][$i] = $i + 1;
                    }
                }
                $params = [$id];
                $this->Mdl_users_access_resources->delete_access_resources_by_user_id($params);

                foreach ($_POST['access_resources'] as $accessResource) {
                    $this->Mdl_users_access_resources->save_resources([
                        $accessResource, $id
                    ]);
                }
            }

            redirect('users');
        }

        if ($id && !$this->input->post('btn_submit', true)) {
            if (!$this->Mdl_users->prep_form($id)) {
                show_404();
            }

            $userCustom = $this->Mdl_custom_fields->by_table('ip_user_custom', $id);

            if ($userCustom->num_rows()) {
                $userCustom = $userCustom->result_array();

                foreach ($userCustom as $key => $val) {
                    $this->Mdl_users->set_form_value("custom['{$val['custom_field_column']}']", $val['value_data']);
                }
            }

        } elseif ($this->input->post('btn_submit', true)) {
            if ($this->input->post('custom', true)) {
                foreach ($this->input->post('custom', true) as $key => $val) {
                    $this->Mdl_users->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        switch ($this->userType) {
            case 0 :
                $companies = $this->Mdl_companies->get_all_companies();
                $user_types = [
                    '0' => lang('master_admin'),
                    '1' => lang('administrator'),
                    '2' => lang('guest_read_only'),
                    '3' => lang('company_admin'),
                ];
                break;
            case 3 :
                //company admin
                $companies = $this->Mdl_companies->getByPk($this->companyId);
                $user_types = [
                    '1' => lang('administrator'),
                    '2' => lang('guest_read_only'),
                    '3' => lang('company_admin')
                ];
                break;
            case 1 :
                //normal user
                $companies = $this->Mdl_companies->getByPk($this->companyId);
                $user_types = ['1' => lang('administrator')];
                break;
        }

        $this->layout->set([
            'settings'                   => $this->Mdl_email->settings(),
            'id'                         => $id,
            'all_companies'              => $companies, //$this->Mdl_companies->get_all_companies(),
            'user_types'                 => $user_types, //$this->Mdl_users->user_types(),
            'user_clients'               => $this->Mdl_user_clients->where('ip_user_clients.user_id', $id)->get()->result(),
            'custom_fields'              => $this->Mdl_custom_fields->by_table('ip_user_custom')->result(),
            'countries'                  => get_country_list(lang('cldr')),
            'selected_country'           => $this->Mdl_users->form_value('user_country') ?: $this->Mdl_settings->setting('default_country'),
            'access_resources'           => $accessResources,
            'preloaded_access_resources' => $preloadedAccessResources
        ]);

        $this->layout
            ->buffer('user_client_table', 'users/partial_user_client_table')
            ->buffer('modal_user_client', 'users/modal_user_client')
            ->buffer('content', 'users/form')
            ->render();
    }

    /**
     * Change password
     * @param $user_id
     */
    public function change_password($user_id)
    {
        switch ($this->userType) {
            case 0 :
                break;
            case 3 :
                //company admin
                if ($user_id && $this->Mdl_users->get_by_id($user_id)->company_id != $this->companyId) {
                    show_404();
                }
                break;
            case 1 :
                //normal user
                if (!$user_id) {
                    show_404();
                }

                if ($this->Mdl_users->get_by_id($user_id)->user_id != $this->companyId) {
                    show_404();
                }
                break;
        }

        if ($this->input->post('btn_cancel', true)) {
            redirect('users');
        }

        if ($this->Mdl_users->run_validation('validation_rules_change_password')) {
            $this->Mdl_users->changePassword($user_id, $this->input->post('user_password', true));
            redirect('users/form/' . $user_id);
        }

        $this->layout
            ->buffer('content', 'users/form_change_password')
            ->render();
    }

    /**
     * Delete
     * @param $id
     */
    public function delete($id)
    {
        if ($id != 1) {
            $this->Mdl_users->delete($id);
        }

        redirect('users');
    }

    /**
     * Delete user client
     * @param $userId
     * @param $userClientId
     */
    public function delete_user_client($userId, $userClientId)
    {
        $this->Mdl_user_clients->delete($userClientId);
        redirect('users/form/' . $userId);
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
            $this->Mdl_users->settings_update();
        }
    }
}
