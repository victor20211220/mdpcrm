<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_invoice_tax_rates extends Response_Model
{
    public $table = 'ip_invoice_tax_rates';
    public $primary_key = 'ip_invoice_tax_rates.invoice_tax_rate_id';

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('ip_tax_rates.tax_rate_name AS invoice_tax_rate_name');
        $this->db->select('ip_tax_rates.tax_rate_percent AS invoice_tax_rate_percent');
        $this->db->select('ip_invoice_tax_rates.*');
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_tax_rates', 'ip_tax_rates.tax_rate_id = ip_invoice_tax_rates.tax_rate_id');
    }

    /**
     * Save
     * @param null $invoiceId
     * @param null $id
     * @param null $data
     */
    public function save($invoiceId, $id = null, $data = null)
    {
        parent::save($id, $data);

        $this->load->model('invoices/Mdl_invoice_amounts');
        $this->Mdl_invoice_amounts->calculate($invoiceId);
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'invoice_id'       => [
                'field' => 'invoice_id',
                'label' => lang('invoice'),
                'rules' => 'required'
            ],
            'tax_rate_id'      => [
                'field' => 'tax_rate_id',
                'label' => lang('tax_rate'),
                'rules' => 'required'
            ],
            'include_item_tax' => [
                'field' => 'include_item_tax',
                'label' => lang('tax_rate_placement'),
                'rules' => 'required'
            ]
        ];
    }
}
