<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//TODO: move this functional to invoices
class Mdl_item_amounts extends CI_Model
{
    /**
     * Calculate
     * @param $itemId
     */
    public function calculate($itemId)
    {
        $this->load->model('invoices/Mdl_items');

        $item = $this->Mdl_items->get_by_id($itemId);

        $item_subtotal = $item->item_quantity * $item->item_price;
        $item_discount_total = $item->item_discount_amount * $item->item_quantity;
        $item_tax_total = ($item_subtotal - $item_discount_total) * ($item->item_tax_rate_percent / 100);
        $item_total = $item_subtotal - $item_discount_total + $item_tax_total;

        $data = [
            'item_id'        => $itemId,
            'item_subtotal'  => $item_subtotal,
            'item_tax_total' => $item_tax_total,
            'item_discount'  => $item_discount_total,
            'item_total'     => $item_total
        ];

        $this->db->where('item_id', $itemId);
        if ($this->db->get('ip_invoice_item_amounts')->num_rows()) {
            $this->db->where('item_id', $itemId);
            $this->db->update('ip_invoice_item_amounts', $data);
        } else {
            $this->db->insert('ip_invoice_item_amounts', $data);
        }
    }
}
