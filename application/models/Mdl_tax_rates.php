<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_tax_rates extends Response_Model
{
    public $table = 'ip_tax_rates';
    public $primary_key = 'ip_tax_rates.tax_rate_id';

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
        $this->db->order_by('ip_tax_rates.tax_rate_percent');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'tax_rate_name'    => [
                'field' => 'tax_rate_name',
                'label' => lang('tax_rate_name'),
                'rules' => 'required'
            ],
            'tax_rate_percent' => [
                'field' => 'tax_rate_percent',
                'label' => lang('tax_rate_percent'),
                'rules' => 'required'
            ],
            'company_id'       => ['field' => 'company_id']
        ];
    }
}
