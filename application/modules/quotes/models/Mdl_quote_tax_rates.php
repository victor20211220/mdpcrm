<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_quote_tax_rates extends Response_Model
{
    public $table = 'ip_quote_tax_rates';
    public $primary_key = 'ip_quote_tax_rates.quote_tax_rate_id';

    public function default_select()
    {
        $this->db->select('ip_tax_rates.tax_rate_name AS quote_tax_rate_name');
        $this->db->select('ip_tax_rates.tax_rate_percent AS quote_tax_rate_percent');
        $this->db->select('ip_quote_tax_rates.*');
    }

    public function default_join()
    {
        $this->db->join('ip_tax_rates', 'ip_tax_rates.tax_rate_id = ip_quote_tax_rates.tax_rate_id');
    }

    public function save($quote_id, $id = NULL, $db_array = NULL)
    {
        parent::save($id, $db_array);

        $this->load->model('quotes/Mdl_quote_amounts');
        $this->Mdl_quote_amounts->calculate($quote_id);
    }

    public function validation_rules()
    {
        return array(
            'quote_id' => array(
                'field' => 'quote_id',
                'label' => lang('quote'),
                'rules' => 'required'
            ),
            'tax_rate_id' => array(
                'field' => 'tax_rate_id',
                'label' => lang('tax_rate'),
                'rules' => 'required'
            ),
            'include_item_tax' => array(
                'field' => 'include_item_tax',
                'label' => lang('tax_rate_placement'),
                'rules' => 'required'
            )
        );
    }

}
