<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_quote_items extends Response_Model
{
    public $table = 'ip_quote_items';
    public $primary_key = 'ip_quote_items.item_id';
    public $date_created_field = 'item_date_added';

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('ip_quote_item_amounts.*, ip_quote_items.*, item_tax_rates.tax_rate_percent AS item_tax_rate_percent');
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_quote_items.item_order');
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_quote_item_amounts', 'ip_quote_item_amounts.item_id = ip_quote_items.item_id', 'left');
        $this->db->join('ip_tax_rates AS item_tax_rates',
            'item_tax_rates.tax_rate_id = ip_quote_items.item_tax_rate_id', 'left');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'quote_id'         => [
                'field' => 'quote_id',
                'label' => lang('quote'),
                'rules' => 'required'
            ],
            'item_name'        => [
                'field' => 'item_name',
                'label' => lang('item_name'),
                'rules' => 'required'
            ],
            'item_description' => [
                'field' => 'item_description',
                'label' => lang('description')
            ],
            'item_quantity'    => [
                'field' => 'item_quantity',
                'label' => lang('quantity'),
                'rules' => 'required'
            ],
            'item_price'       => [
                'field' => 'item_price',
                'label' => lang('price'),
                'rules' => 'required'
            ],
            'item_tax_rate_id' => [
                'field' => 'item_tax_rate_id',
                'label' => lang('item_tax_rate')
            ]
        ];
    }

    /**
     * Save
     * @param null $quoteId
     * @param null $id
     * @param null $data
     * @return null
     */
    public function save($quoteId, $id = null, $data = null)
    {
        $id = parent::save($id, $data);

        $this->load->model('quotes/Mdl_quote_item_amounts');
        $this->Mdl_quote_item_amounts->calculate($id);

        $this->load->model('quotes/Mdl_quote_amounts');
        $this->Mdl_quote_amounts->calculate($quoteId);

        return $id;
    }

    /**
     * Delete
     * @param $id
     * @param bool $setFlash
     */
    public function delete($id, $setFlash = true)
    {
        $this->db->select('quote_id');
        $this->db->where('item_id', $id);
        $quoteId = $this->db->get('ip_quote_items')->row()->quote_id;

        parent::delete($id);

        $this->db->where('item_id', $id);
        $this->db->delete('ip_quote_item_amounts');

        $this->load->model('quotes/Mdl_quote_amounts');
        $this->Mdl_quote_amounts->calculate($quoteId);
    }
}
