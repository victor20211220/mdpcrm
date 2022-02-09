<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_companies extends Response_Model
{
    public $table = 'ip_companies';
    public $primary_key = 'ip_companies.company_id';

    /**
     * Get all companies
     * @return array
     */
    public function get_all_companies()
    {
        $query = $this->db->get('ip_companies');
        $data = [];

        foreach ($query->result() as $row) {
            $data[$row->company_id] = $row->company_name;
        }

        return $data;
    }

    /**
     * Get by id
     * TODO: refactor
     * @param null $id
     * @return array
     */
    public function get_by_id($id = null)
    {
        $query = $this->db->get_where('ip_companies', ['company_id' => $id]);
        $data = [];

        foreach ($query->result() as $row) {
            $data[$row->company_id] = $row->company_name;
        }

        return $data;
    }

    /**
     * Get array by id
     * @param null $id
     * @return array
     */
    public function get_array_by_id($id = null)
    {
        $query = $this->db->get_where('ip_companies', ['company_id' => $id]);
        $data = [];

        foreach ($query->result() as $row) {
            $data['company_id'] = $row->company_id;
            $data['company_name'] = $row->company_name;
            $data['company_country'] = $row->company_country;
            $data['company_address'] = $row->company_address;
            $data['company_code'] = $row->company_code;
            $data['company_vatregnumber'] = $row->company_vatregnumber;
            $data['company_iban'] = $row->company_iban;
            $data['company_bank_bic'] = $row->company_bank_bic;
            $data['company_url'] = $row->company_url;
        }

        return $data;
    }

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_companies.* ', false);
    }

    /**
     * Default join
     */
    public function default_join()
    {
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_companies.company_name');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'company_name'     => [
                'field' => 'company_name',
                'label' => lang('name'),
                'rules' => 'required'
            ],
            'company_country'  => [
                'field' => 'company_country',
                'label' => lang('company_country'),
                'rules' => 'required'
            ],
            'company_address'  => [
                'field' => 'company_address',
                'label' => lang('company_address'),
                'rules' => 'required'
            ],
            'company_bank_bic' => [
                'field' => 'company_bank_bic',
                'label' => 'SWIFT',
                'rules' => 'required|callback_company_bank_bic'
            ],
            'company_iban'     => [
                'field' => 'company_iban',
                'label' => 'IBAN',
                'rules' => 'required|callback_company_iban'
            ],
            'company_url'      => [
                'field' => 'company_url',
                'label' => 'Company_url',
                'rules' => 'required|url'
            ]
        ];
    }

    /**
     * Check company iban
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
     * Check company bank bic
     * @param $swift
     * @return bool
     */
    function company_bank_bic($swift)
    {
        if ($swift == '') {
            return true;
        }

        $regexp = '/^([a-zA-Z]){4}([a-zA-Z]){2}([0-9a-zA-Z]){2}([0-9a-zA-Z]{3})?$/';

        if (!preg_match($regexp, $swift)) {
            $this->form_validation->set_message('company_bank_bic', 'SWIFT code is not valid', 'Hello World !');

            return false;
        } else {
            return true;
        }
    }

    /**
     * Validation rules existing
     * @return array
     */
    public function validation_rules_existing()
    {
        return [
            'company_name'         => [
                'field' => 'company_name',
                'label' => lang('name'),
                'rules' => 'required'
            ],
            'company_country'      => [
                'field' => 'company_country',
                'label' => lang('company_country'),
                'rules' => 'required'
            ],
            'company_address'      => [
                'field' => 'company_address',
                'label' => lang('company_address'),
                'rules' => 'required'
            ],
            'company_code'         => [
                'field' => 'company_code',
                'label' => lang('company_code'),
                'rules' => 'max_length[12]'
            ],
            'company_vatregnumber' => [
                'field' => 'company_vatregnumber',
                'label' => lang('company_vat'),
                'rules' => 'max_length[12]'
            ],
            'company_bank_bic'     => [
                'field' => 'company_bank_bic',
                'label' => 'SWIFT',
                'rules' => 'callback_company_bank_bic'
            ],
            'company_iban'         => [
                'field' => 'company_iban',
                'label' => 'IBAN',
                'rules' => 'callback_company_iban'
            ]
        ];
    }

    /**
     * Delete
     * @param $id
     */
    public function delete($id)
    {
        parent::delete($id);
        delete_orphans();
    }
}
