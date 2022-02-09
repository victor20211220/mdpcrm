<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_quote_details')) {
    function get_quote_details($quote_id)
    {
        $CI =& get_instance();

        $quote = $CI->db->get_where('ip_quotes', ['quote_id' => $quote_id])->row_array();

        $quoteAmounts = $CI->db->get_where(
            'ip_quote_amounts', ['quote_id' => $quote_id]
        )->row_array();

        $quoteItems = [];
        foreach ($CI->db->get_where('ip_quote_items', ['quote_id' => $quote_id])->result_array() as $row) {
            $amountsRow = $CI->db->get_where(
                'ip_quote_item_amounts', ['item_id' => $row['item_id']]
            )->row_array();

            $quoteItems[] = [
                'item_id'               => $row['item_id'],
                'item_quantity'         => $row['item_quantity'],
                'item_name'             => $row['item_name'],
                'item_description'      => $row['item_description'],
                'item_price'            => $row['item_price'],
                'item_subtotal'         => $amountsRow['item_subtotal'],
                'item_tax_total'        => $amountsRow['item_tax_total'],
                'item_discount'         => $amountsRow['item_discount'],
                'item_total'            => $amountsRow['item_total'],
                'item_discount_percent' => $row['item_discount_percent'],
                'item_tax_percentage'   => $CI->db->get_where(
                    'ip_tax_rates', ['tax_rate_id' => $row['item_tax_rate_id']]
                )->row('tax_rate_percent'),
            ];
        }

        $company = $CI->db->get_where('ip_companies', ['company_id' => $quote['company_id']])->row_array();
        $user = $CI->db->get_where('ip_users', ['user_id' => $quote['user_id']])->row_array();
        $client = $CI->db->get_where('ip_clients', ['client_id' => $quote['client_id']])->row_array();

        return [
            'quote'   => $quote,
            'amounts' => $quoteAmounts,
            'items'   => $quoteItems,
            'company' => $company,
            'user'    => $user,
            'client'  => $client
        ];
    }
}
