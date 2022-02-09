<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_items extends Response_Model
{
    public $table = 'ip_invoice_items';
    public $primary_key = 'ip_invoice_items.item_id';
    public $date_created_field = 'item_date_added';

    /**
     * Mdl_items constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'invoices/Mdl_item_amounts',
            'invoices/Mdl_invoice_amounts'
        ]);
    }

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('ip_invoice_item_amounts.*, ip_invoice_items.*, item_tax_rates.tax_rate_percent AS item_tax_rate_percent');
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_invoice_items.item_order');
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join(
            'ip_invoice_item_amounts', 'ip_invoice_item_amounts.item_id = ip_invoice_items.item_id', 'left'
        );
        $this->db->join(
            'ip_tax_rates AS item_tax_rates', 'item_tax_rates.tax_rate_id = ip_invoice_items.item_tax_rate_id', 'left'
        );
        $this->db->join('ip_products', 'ip_products.product_id = ip_invoice_items.item_product_id', 'left');
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
            'item_product_id'  => [
                'field' => 'item_product_id',
                'label' => lang('item_product_id'),
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
     * @param null $invoiceId
     * @param null $id
     * @param null $data
     * @return null
     */
    public function save($invoiceId, $id = null, $data = null)
    {
        $id = parent::save($id, $data);

        $this->Mdl_item_amounts->calculate($id);
        $this->Mdl_invoice_amounts->calculate($invoiceId);

        return $id;
    }

    /**
     * Save xml
     * @param null $id
     * @param null $data
     * @return null
     */
    public function save_xml($id = null, $data = null)
    {
        $id = parent::save($id, $data);

        $this->Mdl_item_amounts->calculate($id);
        $this->Mdl_invoice_amounts->calculate($id);

        return $id;
    }

    /**
     * Delete
     * @param $itemId
     * @param bool $setFlash
     */
    public function delete($itemId, $setFlash = true)
    {
        $this->db->select('invoice_id');
        $this->db->where('item_id', $itemId);
        $invoiceId = $this->db->get('ip_invoice_items')->row()->invoice_id;

        parent::delete($itemId);

        $this->db->where('item_id', $itemId);
        $this->db->delete('ip_invoice_item_amounts');

        $this->Mdl_invoice_amounts->calculate($invoiceId);
    }

    /**
     * Old items
     * @return array
     */
    public function old_items()
    {
        $data = [];
        $items = $this->db->get('ip_invoice_items')->result_array();
        if ($items) {
            $counter = count($items);
            $i = 0;
            foreach ($items as $row) {
                if (
                    $this->db->get_where('ip_invoices', [
                        'invoice_id' => $row['invoice_id']
                    ])->row('company_id') == $this->session->userdata('company_id')
                ) {
                    $i++;
                    if ($i == $counter) {
                        $fino = '';
                    } else {
                        $fino = ',';
                    }
                    $data[] = "'" . $row['item_name'] . "'" . $fino;
                }
            }
        } else {
            $data[] = '';
        }

        return $data;
    }
}
