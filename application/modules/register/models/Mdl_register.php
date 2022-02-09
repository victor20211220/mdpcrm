<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_register extends Response_Model
{
    public $table = 'ip_companies';
    public $primary_key = 'ip_companies.company_id';

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
            'company_name' => [
                'field' => 'company_name',
                'label' => lang('company_name'),
                'rules' => 'required'
            ],

            'company_country' => [
                'field' => 'company_country',
                'label' => lang('company_country'),
                'rules' => 'required'
            ],

            'user_email' => [
                'field' => 'user_email',
                'label' => lang('email'),
                'rules' => 'required|valid_email|is_unique[ip_users.user_email]'
            ],

            'password' => [
                'field' => 'password',
                'label' => lang('password'),
                'rules' => 'required|min_length[8]'
            ],

            'user_password' => [
                'field' => 'user_password',
                'label' => lang('password'),
                'rules' => 'required|min_length[8]|matches[password]'
            ],

            'license_key' => [
                'field' => 'license_key',
                'label' => lang('license_key'),
                'rules' => 'required|min_length[8]'
            ],
        ];
    }

    /**
     * @return array
     */
    public function db_array()
    {
        return parent::db_array();
    }

    /**
     * Dunno why it's here
     * @param null $id
     * @param null $db_array
     * @return void
     */
    public function save($id = null, $db_array = null)
    {
    }
}
