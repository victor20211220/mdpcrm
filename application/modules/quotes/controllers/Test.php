<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Test extends Admin_Controller
{
    public function quote_to_invoice()
    {
        $quote_id = $this->input->post('quote_id', true);

        if ($quote_id) {
            $quote_row = $this->db->get_where('ip_quotes', ['quote_id' => $quote_id])->row_array(); //get quote data

            /* insert invoices table */
            $data = [
                'company_id'               => $quote_row['company_id'],
                'user_id'                  => $quote_row['user_id'],
                'client_id'                => $quote_row['client_id'],
                'invoice_group_id'         => $this->input->post('invoice_group_id', true),
                'invoice_url_key'          => $quote_row['quote_url_key'],
                'invoice_date_created'     => date('Y-m-d', time()),
                'invoice_time_created'     => date('H:i:S', time()),
                'invoice_date_modified'    => date('Y-m-d H:i:S', time()),
                'invoice_password'         => $this->input->post('invoice_password', true),
                'invoice_date_due'         => date('Y-m-d H:i:S',
                    strtotime($this->input->post('invoice_date_created'))),
                'invoice_number'           => 'INV-' . date('Ymd', time()) . rand(1, 9),
                'invoice_discount_amount'  => $quote_row['quote_discount_amount'],
                'invoice_discount_percent' => $quote_row['quote_discount_percent']
            ];
            $this->db->insert('ip_invoices', $data);

            $invoice_id = $this->db->insert_id(); //get invoice id


            /* insert items */
            $items = [];
            $invoice_item_id = '';
            $quote_item_amount_row = [];
            $invoice_amounts = [];
            foreach ($this->db->get_where('ip_quote_items', ['quote_id' => $quote_id])->result_array() as $quote_item) {
                $items = [
                    'invoice_id'            => $invoice_id,
                    'item_tax_rate_id'      => $quote_item['item_tax_rate_id'],
                    'item_date_added'       => $quote_item['item_date_added'],
                    'item_name'             => $quote_item['item_name'],
                    'item_description'      => $quote_item['item_description'],
                    'item_quantity'         => $quote_item['item_quantity'],
                    'item_price'            => $quote_item['item_price'],
                    'item_order'            => $quote_item['item_order'],
                    'item_discount_amount'  => $quote_item['item_discount_amount'],
                    'item_discount_percent' => $quote_item['item_discount_percent']
                ];
                $this->db->insert('ip_invoice_items', $items);

                $invoice_item_id = $this->db->insert_id();
                $quote_item_amount_row = $this->db->get_where('ip_quote_item_amounts',
                    ['item_id' => $quote_item['item_id']])->row_array();

                $invoice_amounts = [
                    'item_id'        => $invoice_item_id,
                    'item_subtotal'  => $quote_item_amount_row['item_subtotal'],
                    'item_tax_total' => $quote_item_amount_row['item_tax_total'],
                    'item_discount'  => $quote_item_amount_row['item_discount'],
                    'item_total'     => $quote_item_amount_row['item_total'],
                ];
                $this->db->insert('ip_invoice_item_amounts', $invoice_amounts);

            }

            /* insert amount */
            $quote_amount = $this->db->get_where('ip_quote_amounts', ['quote_id' => $quote_id])->row_array();

            $amount_insert = [
                'invoice_id'             => $invoice_id,
                'invoice_sign'           => $invoice_id,
                'invoice_item_subtotal'  => $quote_amount['quote_item_subtotal'],
                'invoice_item_tax_total' => $quote_amount['quote_item_tax_total'],
                'invoice_tax_total'      => $quote_amount['quote_tax_total'],
                'invoice_total'          => $quote_amount['quote_total'],
                'invoice_paid'           => '0',
                'invoice_balance'        => $quote_amount['quote_total']
            ];
            $this->db->insert('ip_invoice_amounts', $amount_insert);

            $this->db->delete('ip_quotes', ['quote_id' => $quote_id]); //delete quote
            $this->db->delete('ip_quote_tax_rates', ['quote_id' => $quote_id]); //delete quote tax rates
            $this->db->delete('ip_quote_custom', ['quote_id' => $quote_id]); //delete quote custom
            $this->db->delete('ip_quote_amounts', ['quote_id' => $quote_id]); //delete quote amounts

            foreach ($this->db->get_where('ip_quote_items', ['quote_id' => $quote_id])->result_array() as $it) {
                $this->db->delete('ip_quote_items', ['item_id' => $it['item_id']]);
                $this->db->delete('ip_quote_item_amounts', ['item_id' => $it['item_id']]);
            }


            $response = [
                'success'    => 1,
                'invoice_id' => $invoice_id
            ];
        } else {
            $response = [
                'success'           => 0,
                'validation_errors' => json_errors()
            ];
        }
        echo json_encode($response);
    }
}
