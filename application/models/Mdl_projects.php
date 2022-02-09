<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_projects extends Response_Model
{
    public $table = 'ip_projects';
    public $primary_key = 'ip_projects.project_id';

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_projects.project_id');
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_projects.client_id', 'left');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'project_name' => [
                'field' => 'project_name',
                'label' => lang('project_name'),
                'rules' => 'required'
            ],
            'client_id'    => [
                'field' => 'client_id',
                'label' => lang('client'),
            ]
        ];
    }
}
