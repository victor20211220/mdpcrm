<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_payment_methods extends Response_Model
{
    public $table = 'ip_payment_methods';
    public $primary_key = 'ip_payment_methods.payment_method_id';

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
    public function order_by()
    {
        $this->db->order_by('ip_payment_methods.payment_method_name');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'payment_method_name' => [
                'field' => 'payment_method_name',
                'label' => lang('payment_method'),
                'rules' => 'required'
            ],
            'company_id'          => [
                'field' => 'company_id',
                'rules' => 'required'
            ]
        ];
    }
}
