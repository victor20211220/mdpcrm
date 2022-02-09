<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_families extends Response_Model
{
    public $table = 'ip_families';
    public $primary_key = 'ip_families.family_id';

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
        $this->db->order_by('ip_families.family_name');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'family_name' => [
                'field' => 'family_name',
                'label' => lang('family_name'),
                'rules' => 'required'
            ],
            'company_id'  => [
                'field' => 'company_id',
                'rules' => 'required'
            ]
        ];
    }

}
