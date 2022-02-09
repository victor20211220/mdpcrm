<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_settings extends CI_Model
{
    public $table = 'ip_settings';
    public $primary_key = 'ip_settings.setting_id';
    public $settings = [];

    /**
     * Get value
     * @param $key
     * @param null $companyId
     * @return null
     */
    public function get($key, $companyId = null)
    {
        if ($companyId === null) {
            $companyId = $this->session->userdata('company_id');
        }

        $this->db->select('setting_value');
        $this->db->where('setting_key', $key);
        $this->db->where('company_id', $companyId);
        $query = $this->db->get('ip_settings');

        return $query->row() ? $query->row()->setting_value : null;
    }

    /**
     * Save
     * @param $key
     * @param $value
     * @param null $companyId
     */
    public function save($key, $value, $companyId = null)
    {
        if ($companyId === null) {
            $companyId = $this->session->userdata('company_id');
        }

        $db_array = [
            'setting_key'   => $key,
            'setting_value' => $value,
            'company_id'    => $companyId
        ];

        // check curency standard!!1

        if ($key == 'currency_symbol' and strlen($value) != 3) {
            $CI->session->set_flashdata('alert_error', lang('currency_3_letter'));

            return;
        }

        if ($this->get($key, $companyId) !== null) {
            $this->db->where('setting_key', $key);
            $this->db->where('company_id', $companyId);
            $this->db->update('ip_settings', $db_array);
        } else {
            $this->db->insert('ip_settings', $db_array);
        }

        if (
            $this->input->post('company_name', true) != '' ||
            $this->input->post('company_address', true) != '' ||
            $this->input->post('company_country', true) != '' ||
            $this->input->post('company_code', true) != '' ||
            $this->input->post('company_vatregnumber', true) != '' ||
            $this->input->post('company_iban', true) != '' ||
            $this->input->post('company_bank_bic', true) != '' ||
            $this->input->post('company_url') != ''
        ) {
            $this->load->model('Mdl_settings');

            $data = [
                'company_name'         => $this->input->post('company_name', true),
                'company_address'      => $this->input->post('company_address', true),
                'company_country'      => $this->input->post('company_country', true),
                'company_code'         => $this->input->post('company_code', true),
                'company_vatregnumber' => $this->input->post('company_vatregnumber', true),
                'company_iban'         => $this->input->post('company_iban', true),
                'company_bank_bic'     => $this->input->post('company_bank_bic', true),
                'company_url'          => $this->input->post('company_url', true)
            ];

            if (
                $this->db->get_where('ip_companies',
                ['company_id' => $this->session->userdata('company_id')])->row_array()
            ) {
                $this->db->where('company_id', $this->session->userdata('company_id'));
                $this->db->update('ip_companies', $data);
            } else {
                $this->db->insert('ip_companies', $data);
            }
        }
    }

    /**
     * Company iban
     * @param $swift
     * @return bool
     */
    function company_iban($swift)
    {
        if ($swift == '') {
            return true;
        }

        if (!verify_iban($swift, $machine_format_only = false)) {
            $this->form_validation->set_message('company_iban', 'IBAN code is not valid', 'Hello World !');

            return false;
        }
    }

    /**
     * Company bank bic
     * @param $swift
     * @return bool
     */
    function company_bank_bic($swift)
    {
        if ($swift == '') {
            return true;
        }

        $regexp = '/^([a-zA-Z]){4}([a-zA-Z]){2}([0-9a-zA-Z]){2}([0-9a-zA-Z]{3})?$/';
        $match = preg_match($regexp, $swift);
        if (!$match) {
            $this->form_validation->set_message('company_bank_bic', 'SWIFT code is not valid', 'Hello World !');
        }

        return (boolean) $match;
    }

    /**
     * Check license key
     * @param $licenseKey
     * @return bool
     */
    function check_license_key($licenseKey)
    {
        $this->db->select('setting_value');
        $this->db->where('setting_key', 'license_key');
        $this->db->where('setting_value', $licenseKey);
        $query = $this->db->get('ip_settings');

        return $query->row() ? true : false;
    }

    /**
     * Delete from database by key
     * @param $key
     */
    public function delete($key)
    {
        $this->db->where('setting_key', $key);
        $this->db->where('company_id', $this->session->userdata('company_id'));
        $this->db->delete('ip_settings');
    }

    /**
     * Load settings
     */
    public function load_settings()
    {
        $companyId = $this->session->userdata('company_id');
        $data = $this->db->get_where('ip_settings', ['company_id' => $companyId])->result();
        foreach ($data as $d) {
            $this->settings[$d->setting_key] = $d->setting_value;
        }
    }

    /**
     * Get settings value by key
     * @param $key
     * @return mixed|string
     */
    public function setting($key)
    {
        return (isset($this->settings[$key])) ? $this->settings[$key] : '';
    }

    /**
     * Set settings value
     * @param $key
     * @param $value
     */
    public function set_setting($key, $value)
    {
        $this->settings->$key = $value;
    }

    /**
     * Replicate company settings
     * @param $sourceCompanyId
     * @param $targetCompanyId
     */
    public function replicate_company_settings($sourceCompanyId, $targetCompanyId)
    {
        $defaultSettingsArray = $this->db->get_where('ip_settings', ['company_id' => $sourceCompanyId])->result();
        foreach ($defaultSettingsArray as $setting) {
            $this->save($setting->setting_key, $setting->setting_value, $targetCompanyId);
        }
    }
}
