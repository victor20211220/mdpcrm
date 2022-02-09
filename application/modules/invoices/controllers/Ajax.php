<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
            'invoices/Mdl_items',
            'invoices/Mdl_invoices',
            'invoices/Mdl_invoice_amounts',
            'invoices/Mdl_invoice_tax_rates',
            'invoices/Mdl_invoices_suppliers',
            'invoices/Mdl_invoices_recurring',
            'suppliers/Mdl_suppliers',
            'Mdl_custom_fields_data',
            'Mdl_invoices_recurring',
            'Mdl_products',
            'Mdl_item_lookups',
            'Mdl_stock_alert',
            'Mdl_received_inv_alert',
            'Mdl_invoice_groups',
            'Mdl_tax_rates',
            'Mdl_clients'
        ]);
    }

    /**
     * Save invoice with items
     */
    public function save()
    {
        $invoiceId = $this->input->post('invoice_id', true);
        $_POST['company_id'] = $this->companyId;
        $this->Mdl_invoices->set_id($invoiceId);

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
                $item->item_discount_amount = floatval($item->item_discount_amount);
                $itemId = ($item->item_id) ?: NULL;

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

                $this->Mdl_items->save($invoiceId, $itemId, $item);
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

        $invoice_status = $this->input->post('invoice_status_id', true);

        $db_array = [
            'invoice_number'           => $this->input->post('invoice_number', true),
            'invoice_terms'            => $this->input->post('invoice_terms', true),
            'invoice_date_created'     => date_to_mysql($this->input->post('invoice_date_created', true)),
            'invoice_date_due'         => date_to_mysql($this->input->post('invoice_date_due', true)),
            'invoice_password'         => $this->input->post('invoice_password', true),
            'invoice_status_id'        => $invoice_status,
            'payment_method'           => $this->input->post('payment_method', true),
            'invoice_discount_amount'  => $this->input->post('invoice_discount_amount', true),
            'invoice_discount_percent' => $this->input->post('invoice_discount_percent', true),
        ];

        if (
            $invoice_status == Mdl_invoices::STATUS_SENT &&
            $this->config->item('disable_read_only') == null &&
            $this->Mdl_settings->setting('read_only_toggle') == 'sent'
        ) {
            $db_array['is_read_only'] = 1;
        }

        if (
            $invoice_status == Mdl_invoices::STATUS_VIEWED &&
            $this->config->item('disable_read_only') == null &&
            $this->Mdl_settings->setting('read_only_toggle') == 'viewed'
        ) {
            $db_array['is_read_only'] = 1;
        }

        if (
            $invoice_status == Mdl_invoices::STATUS_PAID &&
            $this->config->item('disable_read_only') == null &&
            $this->Mdl_settings->setting('read_only_toggle') == 'paid'
        ) {
            $db_array['is_read_only'] = 1;
        }

        if (
            $invoice_status == Mdl_invoices::STATUS_SENT &&
            $this->input->post('is_received', true) == 0
        ) {
            foreach ($items as $item) {
                if ($item->item_product_id == 0) {
                    continue;
                }

                $this->Mdl_products->decrease_product_stock($item->item_product_id, $item->item_quantity);
                $this->Mdl_stock_alert->check_alert($item->item_product_id, $this->session->userdata('company_id'));
                $this->Mdl_received_inv_alert->check_alert($this->Mdl_invoices->get_by_id($invoiceId)->client_id);
            }
        }

        $this->Mdl_invoices->save($invoiceId, $db_array);
        $this->Mdl_invoice_amounts->calculate($invoiceId);

        if ($this->input->post('custom', true)) {
            $db_array = [];
            foreach ($this->input->post('custom', true) as $custom) {
                $db_array[str_replace(']', '', str_replace('custom[', '', $custom['name']))] = $custom['value'];
            }

            $this->Mdl_custom_fields_data->save_custom($invoiceId, $db_array, 'ip_invoice_custom');
        }

        $this->session->set_flashdata('alert_success', lang('invoice_saved'));
        echo json_encode(['success' => 1]);
    }

    /**
     * Save invoice tax rates
     */
    public function save_invoice_tax_rate()
    {
        if ($this->Mdl_invoice_tax_rates->run_validation()) {
            $this->Mdl_invoice_tax_rates->save($this->input->post('invoice_id', true));
            $response = ['success' => 1];
        } else {
            $response = [
                'success'           => 0,
                'validation_errors' => $this->Mdl_invoice_tax_rates->validation_errors
            ];
        }

        echo json_encode($response);
    }

    /**
     * Create invoice
     */
    public function create()
    {
        $_POST['company_id'] = $this->companyId;
        if ($this->Mdl_invoices->run_validation()) {
            $invoice_id = $this->Mdl_invoices->create();
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

    /**
     * Create received invoice
     */
    public function create_received()
    {
        $_POST['company_id'] = $this->companyId;
        $_POST['invoice_number'] = '12345';
        $_POST['is_received'] = 1;

        if ($this->Mdl_invoices_suppliers->run_validation()) {
            $invoice_id = $this->Mdl_invoices_suppliers->create_suppliers();
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

    /**
     * Create recurring invoice
     */
    public function create_recurring()
    {
        $_POST['company_id'] = $this->companyId;

        if ($this->Mdl_invoices_recurring->run_validation()) {
            $this->Mdl_invoices_recurring->save();
            $response = ['success' => 1,];
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
        echo json_encode($this->Mdl_items->get_by_id($this->input->post('item_id', true)));
    }

    /**
     * Create invoice
     */
    public function modal_create_invoice()
    {
        $this->load->module('layout');

        $invoiceGroups = $this->Mdl_invoice_groups->getList($this->companyId, null);
        $clients = $this->Mdl_clients
            ->filter_where('ip_clients.company_id', $this->companyId)
            ->get()
            ->result();

        $data = [
            'invoice_groups'    => $invoiceGroups,
            'tax_rates'         => $this->Mdl_tax_rates->get()->result(),
            'clients'           => $clients,
            'client_id'         => '-1',
            'client_name'       => '',
            'client_reg_number' => '',
            'client_address_1'  => '',
            'client_vat_id'     => ''
        ];

        $clientId = $this->input->post('client_id', true);
        if ($clientId) {
            $clientData = $this->Mdl_clients
                ->filter_where('ip_clients.client_id', $clientId)
                ->filter_where('ip_clients.company_id', $this->companyId)
                ->get()
                ->result();

            if (count($clientData) > 0) {
                $data['client_id'] = $clientData[0]->client_id;
                $data['client_name'] = $clientData[0]->client_name;
                $data['client_reg_number'] = $clientData[0]->client_reg_number;
                $data['client_address_1'] = $clientData[0]->client_address_1;
                $data['client_vat_id'] = $clientData[0]->client_vat_id;
            }
        }

        $this->layout->load_view('invoices/modal_create_invoice', $data);
    }

    /**
     * Modal create received invoice
     */
    public function modal_create_received_invoice()
    {
        $this->load->module('layout');

        $invoiceGroups = $this->Mdl_invoice_groups->getList($this->companyId, Mdl_invoice_groups::TYPE_RECEIVED);

        $suppliers = $this->Mdl_suppliers
            ->filter_where('ip_suppliers.company_id', $this->companyId)
            ->get()->result();

        $data = [
            'invoice_groups'      => $invoiceGroups,
            'tax_rates'           => $this->Mdl_tax_rates->get()->result(),
            'suppliers'           => $suppliers,
            'supplier_id'         => '-1',
            'supplier_name'       => '',
            'supplier_reg_number' => '',
            'supplier_address_1'  => '',
            'supplier_vat_id'     => ''
        ];

        $supplierId = $this->input->post('supplier_id', true);
        if ($supplierId) {
            $supplierData = $this->Mdl_suppliers
                ->filter_where('ip_suppliers.supplier_id', $supplierId)
                ->filter_where('ip_suppliers.company_id', $this->companyId)
                ->get()
                ->result();

            if (count($supplierData) > 0) {
                $data['supplier_id'] = $supplierData[0]->supplier_id;
                $data['supplier_name'] = $supplierData[0]->supplier_name;
                $data['supplier_reg_number'] = $supplierData[0]->supplier_reg_number;
                $data['supplier_address_1'] = $supplierData[0]->supplier_address_1;
                $data['supplier_vat_id'] = $supplierData[0]->supplier_vat_id;
            }
        }

        $this->layout->load_view('invoices/modal_create_received_invoice', $data);
    }

    /**
     * Modal create recurring
     */
    public function modal_create_recurring()
    {
        $this->load->module('layout');

        $this->layout->load_view('invoices/modal_create_recurring', [
            'invoice_id'        => $this->input->post('invoice_id', true),
            'recur_frequencies' => $this->Mdl_invoices_recurring->getRecurringFrequencies()
        ]);
    }

    /**
     * Ger recurring start date
     */
    public function get_recur_start_date()
    {
        $invoiceDate = $this->input->post('invoice_date', true);
        $recurringFrequency = $this->input->post('recur_frequency', true);

        echo increment_user_date($invoiceDate, $recurringFrequency);
    }

    /**
     * Change client
     */
    public function modal_change_client()
    {
        $this->load->module('layout');

        $clients = $this->Mdl_clients->filter_where('ip_clients.company_id', $this->companyId)->get()->result();
        $this->layout->load_view('invoices/modal_change_client', [
            'client_name' => $this->input->post('client_name', true),
            'invoice_id'  => $this->input->post('invoice_id', true),
            'clients'     => $clients
        ]);
    }

    /**
     * Change supplier
     */
    public function modal_change_supplier()
    {
        $this->load->module('layout');

        $suppliers = $this->Mdl_suppliers->filter_where('ip_suppliers.company_id', $this->companyId)->get()->result();
        $this->layout->load_view('invoices/modal_change_supplier', [
            'supplier_name' => $this->input->post('supplier_name', true),
            'invoice_id'    => $this->input->post('invoice_id', true),
            'suppliers'     => $suppliers
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
            $invoiceId = $this->input->post('invoice_id', true);

            $this->db->where('invoice_id', $invoiceId);
            $this->db->update('ip_invoices', ['client_id' => $clientId]);
            $response = [
                'success'    => 1,
                'invoice_id' => $invoiceId
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
     * Change invoice supplier
     */
    public function change_supplier()
    {
        $invoiceId = $this->input->post('invoice_id', true);
        $supplierName = $this->input->post('supplier_name', true);

        $supplier = $this->Mdl_suppliers
            ->where('supplier_name', $this->db->escape_str($supplierName))
            ->get()
            ->row();

        if (!empty($supplier)) {
            $supplierId = $supplier->supplier_id;

            $this->db->where('invoice_id', $invoiceId);
            $this->db->update('ip_invoices', ['supplier_id' => $supplierId]);
            $response = [
                'success'    => 1,
                'invoice_id' => $invoiceId
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
     * Modal copy invoice
     */
    public function modal_copy_invoice()
    {
        $this->load->module('layout');

        $invoiceId = $this->input->post('invoice_id', true);

        $invoiceGroups = $this->Mdl_invoice_groups
            ->filter_where('ip_invoice_groups.company_id', $this->companyId)
            ->get()
            ->result();

        $taxRates = $this->Mdl_tax_rates
            ->filter_where('ip_tax_rates.company_id', $this->companyId)
            ->get()
            ->result();

        $clients = $this->Mdl_clients
            ->filter_where('ip_clients.company_id', $this->companyId)
            ->get()
            ->result();

        $invoice = $this->Mdl_invoices
            ->where('ip_invoices.invoice_id', $invoiceId)
            ->get()
            ->row();

        $this->layout->load_view('invoices/modal_copy_invoice', [
            'invoice_groups' => $invoiceGroups,
            'tax_rates'      => $taxRates,
            'invoice_id'     => $invoiceId,
            'invoice'        => $invoice,
            'clients'        => $clients
        ]);
    }

    /**
     * Copy invoice
     */
    public function copy_invoice()
    {
        $_POST['company_id'] = $this->companyId;
        if ($this->Mdl_invoices->run_validation()) {
            $targetId = $this->Mdl_invoices->save();
            $sourceId = $this->input->post('invoice_id', true);

            $this->Mdl_invoices->copy_invoice($sourceId, $targetId);
            $response = [
                'success'    => 1,
                'invoice_id' => $targetId
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
     * Modal create credit
     */
    public function modal_create_credit()
    {
        $this->load->module('layout');

        $invoiceId = $this->input->post('invoice_id', true);

        $invoiceGroups = $this->Mdl_invoice_groups
            ->filter_where('ip_invoice_groups.company_id', $this->companyId)
            ->get()
            ->result();

        $taxRates = $this->Mdl_tax_rates
            ->filter_where('ip_tax_rates.company_id', $this->companyId)
            ->get()
            ->result();

        $invoice = $this->Mdl_invoices
            ->where('ip_invoices.invoice_id', $invoiceId)
            ->get()
            ->row();

        $this->layout->load_view('invoices/modal_create_credit', [
            'invoice_groups' => $invoiceGroups,
            'tax_rates'      => $taxRates,
            'invoice_id'     => $invoiceId,
            'invoice'        => $invoice
        ]);
    }

    /**
     * Create credit
     */
    public function create_credit()
    {
        if ($this->Mdl_invoices->run_validation()) {
            $targetId = $this->Mdl_invoices->save();
            $sourceId = $this->input->post('invoice_id', true);
            $this->Mdl_invoices->copy_credit_invoice($sourceId, $targetId);

            if ($this->config->item('disable_read_only') == false) {
                $this->Mdl_invoices->where('invoice_id', $sourceId);
                $this->Mdl_invoices->update('ip_invoices', ['is_read_only' => '1']);
            }

            $this->Mdl_invoices->where('invoice_id', $targetId);
            $this->Mdl_invoices->update('ip_invoices', ['creditinvoice_parent_id' => $sourceId]);
            $this->Mdl_invoices->where('invoice_id', $targetId);
            $this->Mdl_invoices->update('ip_invoice_amounts', ['invoice_sign' => '-1']);
            $response = [
                'success'    => 1,
                'invoice_id' => $targetId
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
