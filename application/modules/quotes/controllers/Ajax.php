<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    /**
     * Ajax constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'quotes/Mdl_quote_items',
            'quotes/Mdl_quotes',
            'quotes/Mdl_quote_amounts',
            'quotes/Mdl_quote_tax_rates',
            'Mdl_custom_fields_data',
            'Mdl_item_lookups',
            'Mdl_invoice_groups',
            'Mdl_tax_rates',
            'Mdl_clients'
        ]);

        $this->load->library('encrypt');
    }

    /**
     * Save
     */
    public function save()
    {
        $quoteId = $this->input->post('quote_id', true);
        $this->Mdl_quotes->set_id($quoteId);
        $_POST['company_id'] = $this->companyId;

        if ($this->Mdl_quotes->run_validation('validation_rules_save_quote')) {
            $items = json_decode($this->input->post('items', true));
            foreach ($items as $item) {
                if (
                    !empty($item->item_quantity) &&
                    !empty($item->item_price) ||
                    !empty($item->item_name) ||
                    !empty($item->item_description)
                ) {
                    $item->item_quantity = round(floatval($item->item_quantity), 8);
                    $item->item_price = round(floatval($item->item_price), 8);
                    $item->item_discount_amount = standardize_amount($item->item_discount_amount);
                    $itemId = ($item->item_id) ?: null;

                    if (
                        $item->new_row_added == 1 &&
                        $this->input->post('is_received', true) == 0 &&
                        $item->item_product_id < 0
                    ) {
                        $productId = $this->Mdl_products->findProductIdBySearchString($this->companyId, $item->item_name);
                        if ($productId) {
                            $item->item_product_id = $productId;
                        } else {
                            $product['company_id'] = $this->companyId;
                            $product['product_name'] = $item->item_name;
                            $product['product_sku'] = time();
                            $product['product_description'] = $item->item_description;
                            $product['product_price'] = $item->item_price;
                            $product['tax_rate_id'] = $item->item_tax_rate_id;

                            $item->item_product_id = $this->Mdl_products->save(null, $product);
                        }
                    }

                    unset($item->item_id);
                    unset($item->save_item_as_lookup);
                    unset($item->new_row_added);
                    unset($item->product_stock);

                    $this->Mdl_quote_items->save($quoteId, $itemId, $item);
                } else {
                    $this->form_validation->set_rules('item_name', lang('item'), 'required');
                    $this->form_validation->set_rules('item_description', lang('description'), 'required');
                    $this->form_validation->set_rules('item_quantity', lang('quantity'), 'required');
                    $this->form_validation->set_rules('item_price', lang('price'), 'required');
                    $this->form_validation->run();

                    echo json_encode([
                        'success'           => 0,
                        'validation_errors' => [
                            'item_name'        => form_error('item_name', '', ''),
                            'item_description' => form_error('item_description', '', ''),
                            'item_quantity'    => form_error('item_quantity', '', ''),
                            'item_price'       => form_error('item_price', '', ''),
                        ]
                    ]);
                    exit;
                }
            }

            $this->Mdl_quotes->save($quoteId, [
                'quote_number'           => $this->input->post('quote_number', true),
                'quote_date_created'     => date_to_mysql($this->input->post('quote_date_created', true)),
                'quote_date_expires'     => date_to_mysql($this->input->post('quote_date_expires', true)),
                'quote_status_id'        => $this->input->post('quote_status_id', true),
                'quote_password'         => $this->input->post('quote_password', true),
                'notes'                  => $this->input->post('notes', true),
                'quote_discount_amount'  => $this->input->post('quote_discount_amount', true),
                'quote_discount_percent' => $this->input->post('quote_discount_percent', true),
            ]);

            $this->Mdl_quote_amounts->calculate($quoteId);
            $response = ['success' => 1];
        } else {
            $response = [
                'success'           => 0,
                'validation_errors' => json_errors()
            ];
        }

        if ($this->input->post('custom', true)) {
            $customData = [];

            foreach ($this->input->post('custom', true) as $custom) {
                $customData[str_replace(']', '', str_replace('custom[', '', $custom['name']))] = $custom['value'];
            }

            $this->Mdl_custom_fields_data->save_custom($quoteId, $customData, 'ip_quote_custom');
        }

        $this->session->set_flashdata('alert_success', lang('invoice_saved'));
        echo json_encode($response);
    }

    /**
     * Save quote tax rate
     */
    public function save_quote_tax_rate()
    {
        if ($this->Mdl_quote_tax_rates->run_validation()) {
            $this->Mdl_quote_tax_rates->save($this->input->post('quote_id', true));
            $response = ['success' => 1];
        } else {
            $response = [
                'success'           => 0,
                'validation_errors' => json_errors()
            ];
        }

        echo json_encode($response);
    }

    /**
     * Create
     */
    public function create()
    {
        $_POST['company_id'] = $this->companyId;
        if ($this->Mdl_quotes->run_validation()) {
            $quoteId = $this->Mdl_quotes->create();
            $response = [
                'success'  => 1,
                'quote_id' => $quoteId
            ];
        } else {
            $response = [
                'success'           => 0,
                'validation_errors' => json_errors()
            ];
        }

        echo json_encode($response);
    }

    /**
     * Modal change client
     */
    public function modal_change_client()
    {
        $this->load->module('layout');

        $clients = $this->Mdl_clients->filter_where('ip_clients.company_id', $this->companyId)->get()->result();

        $this->layout->load_view('quotes/modal_change_client', [
            'client_name' => $this->input->post('client_name', true),
            'quote_id'    => $this->input->post('quote_id', true),
            'clients'     => $clients
        ]);
    }

    /**
     * Change client
     */
    public function change_client()
    {
        $clientName = $this->input->post('client_name', true);
        $client = $this->Mdl_clients->where('client_name', $this->db->escape_str($clientName))->get()->row();

        if (!empty($client)) {
            $clientId = $client->client_id;
            $quoteId = $this->input->post('quote_id', true);

            $this->db->where('quote_id', $quoteId);
            $this->db->update('ip_quotes', ['client_id' => $clientId]);

            $response = [
                'success'  => 1,
                'quote_id' => $quoteId
            ];
        } else {
            $response = [
                'success'           => 0,
                'validation_errors' => json_errors()
            ];
        }

        echo json_encode($response);
    }

    /**
     * Get item
     */
    public function get_item()
    {
        echo json_encode($this->Mdl_quote_items->get_by_id($this->input->post('item_id', true)));
    }

    /**
     * Modal create quote
     */
    public function modal_create_quote()
    {
        $this->load->module('layout');
        $data = [];

        $clients = $this->Mdl_clients->filter_where('ip_clients.company_id', $this->companyId)->get()->result();
        $invoiceGroups = $this->Mdl_invoice_groups
            ->filter_where('ip_invoice_groups.company_id', $this->companyId)
            ->get()
            ->result();

        $data['invoice_groups'] = $invoiceGroups;
        $data['tax_rates'] = $this->Mdl_tax_rates->get()->result();
        $data['client_name'] = $this->input->post('client_name', true);
        $data['client_id'] = $this->input->post('client_id', true);
        $data['clients'] = $clients;

        $this->layout->load_view('quotes/modal_create_quote', $data);
    }

    /**
     * Modal copy quote
     */

    public function modal_copy_quote()
    {
        $this->load->module('layout');

        $quoteId = $this->input->post('quote_id', true);

        $invoiceGroups = $this->Mdl_invoice_groups
            ->filter_where('ip_invoice_groups.company_id', $this->companyId)
            ->get()
            ->result();

        $taxRates = $this->Mdl_tax_rates
            ->filter_where('ip_tax_rates.company_id', $this->companyId)
            ->get()
            ->result();

        $clients = $this->db
            ->get_where('ip_clients', ['company_id' => $this->companyId])
            ->result_array();

        $quote = $this->Mdl_quotes
            ->where('ip_quotes.quote_id', $quoteId)
            ->get()
            ->row();

        $this->layout->load_view('quotes/modal_copy_quote', [
            'invoice_groups' => $invoiceGroups,
            'tax_rates'      => $taxRates,
            'quote_id'       => $quoteId,
            'quote'          => $quote,
            'clients'        => $clients
        ]);
    }

    /**
     * Copy quote
     */
    public function copy_quote()
    {
        $sourceId = $this->input->post('quote_id', true);
        $this->Mdl_quotes->set_id($sourceId);
        $_POST['company_id'] = $this->companyId;

        if ($this->Mdl_quotes->run_validation()) {
            $targetId = $this->Mdl_quotes->save();
            $this->Mdl_quotes->copy_quote($sourceId, $targetId);

            $response = [
                'success'  => 1,
                'quote_id' => $targetId
            ];
        } else {
            $response = [
                'success'           => 0,
                'validation_errors' => json_errors()
            ];
        }

        echo json_encode($response);
    }

    /**
     * Modal quote to invoice
     * @param $quoteId
     */
    public function modal_quote_to_invoice($quoteId)
    {
        $invoiceGroups = $this->Mdl_invoice_groups
            ->filter_where('ip_invoice_groups.company_id', $this->companyId)
            ->get()
            ->result();

        $quote = $this->Mdl_quotes
            ->where('ip_quotes.quote_id', $quoteId)
            ->get()
            ->row();

        $this->load->view('quotes/modal_quote_to_invoice', [
            'invoice_groups' => $invoiceGroups,
            'quote_id'       => $quoteId,
            'quote'          => $quote
        ]);
    }

    /**
     * Quote to invoice
     */
    public function quote_to_invoice()
    {
        $quoteId = $this->input->post('quote_id', true);
        $invoiceDateCreated = (new DateTime($this->input->post('invoice_date_created')))->format('Y-m-d H:i:s');
        $invoiceGroupId = $this->input->post('invoice_password', true);

        if (!$quoteId) {
            echo json_encode([
                'success'           => 0,
                'validation_errors' => json_errors()
            ]);

            exit();
        }

        $quoteData = $this->db->get_where('ip_quotes', ['quote_id' => $quoteId])->row_array();
        $this->db->insert('ip_invoices', [
            'company_id'               => $quoteData['company_id'],
            'user_id'                  => $quoteData['user_id'],
            'client_id'                => $quoteData['client_id'],
            'invoice_group_id'         => $this->input->post('invoice_group_id', true),
            'invoice_url_key'          => $quoteData['quote_url_key'],
            'invoice_date_created'     => date('Y-m-d', time()),
            'invoice_time_created'     => date('H:i:S', time()),
            'invoice_date_modified'    => date('Y-m-d H:i:S', time()),
            'invoice_password'         => $invoiceGroupId,
            'invoice_date_due'         => $invoiceDateCreated,
            'invoice_number'           => 'INV-' . date('Ymd', time()) . rand(1, 9),
            'invoice_discount_amount'  => $quoteData['quote_discount_amount'],
            'invoice_discount_percent' => $quoteData['quote_discount_percent']
        ]);

        $invoiceId = $this->db->insert_id(); //get invoice id

        $quoteItems = $this->db->get_where('ip_quote_items', ['quote_id' => $quoteId])->result_array();
        foreach ($quoteItems as $quoteItem) {
            $this->db->insert('ip_invoice_items', [
                'invoice_id'            => $invoiceId,
                'item_tax_rate_id'      => $quoteItem['item_tax_rate_id'],
                'item_date_added'       => $quoteItem['item_date_added'],
                'item_name'             => $quoteItem['item_name'],
                'item_description'      => $quoteItem['item_description'],
                'item_quantity'         => $quoteItem['item_quantity'],
                'item_price'            => $quoteItem['item_price'],
                'item_order'            => $quoteItem['item_order'],
                'item_discount_amount'  => $quoteItem['item_discount_amount'],
                'item_discount_percent' => $quoteItem['item_discount_percent']
            ]);

            $invoiceItemId = $this->db->insert_id();
            $quoteItemAmountRow = $this->db
                ->get_where('ip_quote_item_amounts', ['item_id' => $quoteItem['item_id']])
                ->row_array();

            $this->db->insert('ip_invoice_item_amounts', [
                'item_id'        => $invoiceItemId,
                'item_subtotal'  => $quoteItemAmountRow['item_subtotal'],
                'item_tax_total' => $quoteItemAmountRow['item_tax_total'],
                'item_discount'  => $quoteItemAmountRow['item_discount'],
                'item_total'     => $quoteItemAmountRow['item_total'],
            ]);
        }

        $quoteAmount = $this->db->get_where('ip_quote_amounts', ['quote_id' => $quoteId])->row_array();
        $this->db->insert('ip_invoice_amounts', [
            'invoice_id'             => $invoiceId,
            'invoice_sign'           => $invoiceId,
            'invoice_item_subtotal'  => $quoteAmount['quote_item_subtotal'],
            'invoice_item_tax_total' => $quoteAmount['quote_item_tax_total'],
            'invoice_tax_total'      => $quoteAmount['quote_tax_total'],
            'invoice_total'          => $quoteAmount['quote_total'],
            'invoice_paid'           => '0',
            'invoice_balance'        => $quoteAmount['quote_total']
        ]);

        $this->db->delete('ip_quotes', ['quote_id' => $quoteId]);
        $this->db->delete('ip_quote_tax_rates', ['quote_id' => $quoteId]);
        $this->db->delete('ip_quote_custom', ['quote_id' => $quoteId]);
        $this->db->delete('ip_quote_amounts', ['quote_id' => $quoteId]);

        foreach ($quoteItems as $it) {
            $this->db->delete('ip_quote_items', ['item_id' => $it['item_id']]);
            $this->db->delete('ip_quote_item_amounts', ['item_id' => $it['item_id']]);
        }

        echo json_encode([
            'success'    => 1,
            'invoice_id' => $invoiceId
        ]);
    }
}
