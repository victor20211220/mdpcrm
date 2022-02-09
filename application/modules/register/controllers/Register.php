<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends Base_Controller
{
    private $localKey = '';

    /**
     * Register constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_register',
            'Mdl_companies',
            'users/Mdl_users',
            'Mdl_languages'
        ]);
    }

    /**
     * Index action
     * @param int $page
     */
    public function index($page = 0)
    {
        redirect('register/company');
    }

    /**
     * Register company action
     */
    public function company()
    {
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        }

        if ($this->input->post('btn_back_to_login', true)) {
            redirect('sessions/login');
        }

        $displayErrors = true;

        if (count($_GET) > 0) {
            $movedFromGet = false;

            foreach ($_GET as $key => $value) {
                if (isset($_POST[$key]) == false) {
                    $movedFromGet = true;
                    $_POST[$key] = $value;
                }
            }

            if ($movedFromGet === true) {
                $displayErrors = false;
                $_SERVER['REQUEST_METHOD'] = 'POST';
                $_POST['btn_submit'] = '1';
            }
        }

        if (
            $this->input->post('btn_submit', true) &&
            $this->Mdl_register->run_validation('validation_rules')
        ) {
            // Validate license key
            $result = checkProductLicense($this->Mdl_register->form_value('license_key'), $this->getLocalKey());
            if ($result['status'] != 'Active') {
                $this->session->set_flashdata('alert_error', lang('license_error') . strtolower($result['status']));
                redirect('register/company');
            } else {
                $alreadyExist = $this->Mdl_settings->check_license_key($this->Mdl_register->form_value('license_key'));
                if ($alreadyExist) {
                    $this->session->set_flashdata('alert_error', lang('license_error') . strtolower($result['status']));
                    redirect('register/company');
                } else {
                    $this->setLocalKey($result['localkey']);
                }
            }

            $companyId = $this->Mdl_companies->save(null, [
                'company_name'    => $this->Mdl_register->form_value('company_name'),
                'company_country' => $this->Mdl_register->form_value('company_country'),
                'company_address' => $this->Mdl_register->form_value('company_address')
            ]);

            $salt = $this->crypt->salt();
            $email = $this->Mdl_register->form_value('user_email');
            $password = $this->Mdl_register->form_value('user_password');
            $passwordCrypt = $this->crypt->generate_password($password, $salt);

            $userId = $this->Mdl_users->save(null, [
                'user_type'     => 3,
                'user_email'    => $email,
                'user_psalt'    => $salt,
                'user_name'     => $this->Mdl_register->form_value('company_name'),
                'user_password' => $passwordCrypt,
                'user_company'  => $this->Mdl_register->form_value('company_name'),
                'company_id'    => $companyId,
                'user_country'  => $this->input->post('company_country')
            ]);

            //create default settings from zero company
            $this->Mdl_settings->replicate_company_settings(0, $companyId);

            // assign license key and default language company settings
            $this->Mdl_settings->save('license_key', $this->Mdl_register->form_value('license_key'), $companyId);
            $this->Mdl_settings->save('default_language', $this->Mdl_register->form_value('default_language'),
                $companyId);
            $this->Mdl_settings->save('local_license_key', $this->getLocalKey(), $companyId);

            $cronKey = random_string('alnum', 16);
            $companyCountry = $this->input->post('company_country');

            $this->db->insert_batch('ip_settings', [
                ['company_id' => $companyId, 'setting_key' => 'default_country', 'setting_value' => $companyCountry],
                ['company_id' => $companyId, 'setting_key' => 'currency_symbol', 'setting_value' => 'EUR'],
                ['company_id' => $companyId, 'setting_key' => 'thousands_separator', 'setting_value' => ','],
                ['company_id' => $companyId, 'setting_key' => 'decimal_point', 'setting_value' => '.'],
                ['company_id' => $companyId, 'setting_key' => 'first_day_of_week', 'setting_value' => '1'],
                ['company_id' => $companyId, 'setting_key' => 'date_format', 'setting_value' => 'm/d/Y'],
                ['company_id' => $companyId, 'setting_key' => 'invoices_due_after', 'setting_value' => '30'],
                ['company_id' => $companyId, 'setting_key' => 'quotes_expire_after', 'setting_value' => '15'],
                ['company_id' => $companyId, 'setting_key' => 'default_invoice_group', 'setting_value' => '1'],
                ['company_id' => $companyId, 'setting_key' => 'default_quote_group', 'setting_value' => '2'],
                ['company_id' => $companyId, 'setting_key' => 'cron_key', 'setting_value' => $cronKey],
                ['company_id' => $companyId, 'setting_key' => 'tax_rate_decimal_places', 'setting_value' => '2'],
                ['company_id' => $companyId, 'setting_key' => 'pdf_invoice_template', 'setting_value' => 'default'],
                ['company_id' => $companyId, 'setting_key' => 'pdf_quote_template', 'setting_value' => 'default'],
                ['company_id' => $companyId, 'setting_key' => 'public_invoice_template', 'setting_value' => 'default'],
                ['company_id' => $companyId, 'setting_key' => 'public_quote_template', 'setting_value' => 'default'],
                [
                    'company_id'    => $companyId,
                    'setting_key'   => 'pdf_invoice_template_overdue',
                    'setting_value' => 'default'
                ],
                [
                    'company_id'    => $companyId,
                    'setting_key'   => 'currency_symbol_placement',
                    'setting_value' => 'afterspace'
                ],
                [
                    'company_id'    => $companyId,
                    'setting_key'   => 'pdf_invoice_template_paid',
                    'setting_value' => 'default'
                ]
            ]);

            foreach ($this->db->get('ip_access_resources')->result_array() as $row) {
                $this->db->insert('ip_users_access_resources', [
                    'access_resource_id' => $row['access_resource_id'],
                    'user_id'            => $userId
                ]);
            }

            $this->Mdl_sessions->auth($email, $password);

            redirect('dashboard');
        }

        $this->load->view('register/signup', [
            'display_errors'   => $displayErrors,
            'countries'        => get_country_list(lang('cldr')),
            'selected_country' => $this->Mdl_companies->form_value('company_country') ?: 'SE',
            'languages'        => $this->Mdl_languages->get_languages(),
            'model'            => $this->Mdl_register
        ]);
    }

    /**
     * Get local key
     * @return string
     */
    private function getLocalKey()
    {
        return $this->localKey;
    }

    /**
     * Set local key
     * @param $key
     */
    private function setLocalKey($key)
    {
        $this->localKey = $key;
    }
}
