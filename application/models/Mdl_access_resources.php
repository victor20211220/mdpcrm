<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_access_resources extends Response_Model
{
    public $table = 'ip_access_resources';
    public $primary_key = 'ip_access_resources.ip_access_resource_id';

    /**
     * User types
     * @return array
     */
    public function user_types()
    {
        return [
            '2' => lang('guest_read_only'),
            '1' => lang('administrator'),
            '3' => lang('company_admin'),
            '0' => lang('master_admin'),
        ];
    }

    /**
     * Get all
     * @return mixed
     */
    public function get_all()
    {
        $query = $this->db->query("SELECT * FROM ip_access_resources");

        return $query->result_array();
    }
}
