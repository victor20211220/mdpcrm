<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices extends Base_Controller
{
    /**
     * Invoices constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'invoices/Mdl_invoice_tax_rates',
            'invoices/Mdl_invoices',
            'invoices/Mdl_items',
            'Mdl_invoice_groups',
            'Mdl_products',
            'quotes/Mdl_quotes',
            'users/Mdl_users'
        ]);

        $this->load->helper([
            'pdf',
            'string'
        ]);
    }

    /**
     * Index controller
     */
    public function index()
    {
        redirect('guest/invoices/status/open');
    }

    /**
     * By status
     * @param string $status
     * @param int $page
     */
    public function status($status = 'open', $page = 0)
    {
        switch ($status) {
            case 'paid' :
                $this->Mdl_invoices->is_paid()->where_in('ip_invoices.client_id', $this->user_clients);
                break;
            default :
                $this->Mdl_invoices->is_open()->where_in('ip_invoices.client_id', $this->user_clients);
                break;
        }

        $this->Mdl_invoices->paginate(site_url('guest/invoices/status/' . $status), $page);
        $invoices = $this->Mdl_invoices->result();

        $this->layout->set([
            'invoices' => $invoices,
            'status'   => $status
        ]);
        $this->layout->buffer('content', 'guest/invoices_index');
        $this->layout->render('layout_guest');
    }

    /**
     * View invoice
     * @param $invoiceId
     */
    public function view($invoiceId)
    {
        $invoice = $this->Mdl_invoices
            ->where('ip_invoices.invoice_id', $invoiceId)
            ->where_in('ip_invoices.client_id', $this->user_clients)
            ->get()
            ->row();

        if (!$invoice) {
            show_404();
        }

        $this->Mdl_invoices->mark_viewed($invoice->invoice_id);
        $this->layout->set([
            'invoice'           => $invoice,
            'items'             => $this->Mdl_items->where('invoice_id', $invoiceId)->get()->result(),
            'invoice_tax_rates' => $this->Mdl_invoice_tax_rates->where('invoice_id', $invoiceId)->get()->result(),
            'invoice_id'        => $invoiceId
        ]);

        $this->layout->buffer([
            'content',
            'guest/invoices_view'
        ]);

        $this->layout->render('layout_guest');
    }

    /**
     * Generate pdf
     * @param $invoiceId
     * @param bool $stream
     * @param null $template
     */
    public function generate_pdf($invoiceId, $stream = true, $template = null)
    {
        $this->Mdl_invoices->mark_viewed($invoiceId);
        generate_invoice_pdf($invoiceId, $stream, $template, 1);
    }

    public function products($companyHash)
    {
        if (!$companyHash) {
            show_error("No company provided");
        }

        $invoiceGroups = $this->Mdl_invoice_groups
            ->where('md5(company_id)', $companyHash)
            ->where('invoice_group_type', Mdl_invoice_groups::TYPE_RECEIVED)
            ->get();

        if ($invoiceGroups->num_rows() == 0) {
            show_error("This company can't receive invoices (no group created)");
        }

        $group = $invoiceGroups->row();
        $companyId = $group->company_id;

        $products = $this->Mdl_products
            ->where('ip_products.company_id', $companyId)
            ->get()
            ->result();

        foreach ($products as $p) {
            $p->product_price = format_amount($p->product_price);
        }

        echo json_encode($products);
    }

    /**
     * Create invoice procedure
     */
    public function create()
    {
        $url = $_SERVER['HTTP_HOST'];
        $subdomains = explode('.', $url);
        $companyUrl = $subdomains[0];

        if (!$companyUrl) {
            show_error("No company provided");
        }

        $companyId = $this->Mdl_users->getCompanyIdByUrl($companyUrl);
        if (!$companyId) {
            show_error("No company found for create - {$companyUrl}");
        }

        $companyHash = md5($companyId);

        $invoiceGroups = $this->Mdl_invoice_groups
            ->where('md5(company_id)', $companyHash)
            ->where('invoice_group_type', Mdl_invoice_groups::TYPE_RECEIVED)
            ->get();

        if ($invoiceGroups->num_rows() == 0) {
            show_error("This company can't create receive invoices (no group created)");
        }

        $group = $invoiceGroups->row();
        $companyId = $group->company_id;
        $dateTime = new DateTime();
        $urlKey = random_string('alnum', 15);

        $this->db->insert('ip_invoices', [
            'client_id'             => 0,
            'invoice_date_created'  => $dateTime->format('Y-m-d'),
            'invoice_group_id'      => $group->invoice_group_id,
            'invoice_time_created'  => $dateTime->format('H:m:d'),
            'invoice_password'      => '',
            'user_id'               => 3,
            'payment_method'        => 0,
            'company_id'            => $companyId,
            'invoice_url_key'       => $urlKey,
            'invoice_date_modified' => $dateTime->format('Y-m-d H:i:s'),
            'invoice_date_due'      => $dateTime->format('Y-m-d H:i:s'),
            'invoice_number'        => $this->Mdl_invoice_groups->generateInvoiceNumber($group->invoice_group_id),
            'supplier_id'           => 0,
            'time'                  => '00:00:00'
        ]);

        $invoiceId = $this->db->insert_id();
        $invoiceHash = md5($invoiceId);

        $this->db->insert('ip_invoice_amounts', ['invoice_id' => $invoiceId]);

        redirect('http://' . $_SERVER['HTTP_HOST'] . "/guest/invoices/edit/{$invoiceHash}", 'location');
    }

    /**
     * Edit invoice
     * @param $invoiceHash
     */
    public function edit($invoiceHash)
    {
        $companies = $this->Mdl_users->get()->result();
        if (!$companies) {
            show_404();
        }

        $url = $_SERVER['HTTP_HOST'];
        $subdomains = explode('.', $url);
        $companyUrl = $subdomains[0];

        if (!$companyUrl) {
            show_error("No company provided");
        }

        $companyId = $this->Mdl_users->getCompanyIdByUrl($companyUrl);
        if (!$companyId) {
            show_error("No company found for edit - {$companyUrl}");
        }

        $companyHash = md5($companyId);

        $companyInfo = $this->Mdl_users
            ->where('md5(ip_users.company_id)', $companyHash)
            ->get()
            ->row();

        if (!$companyInfo) {
            show_error("Bad company provided for edit - {$companyUrl}");
        }

        $invoiceInfo = $this->Mdl_invoices
            ->where('ip_invoices.company_id', $companyInfo->company_id)
            ->where('md5(ip_invoices.invoice_id)', $invoiceHash)
            ->get()
            ->row();

        if (!$invoiceInfo) {
            show_error("Bad invoice provided for edit - {$companyUrl}");
        }

        $companyId = $companyInfo->company_id;
        $invoiceId = $invoiceInfo->invoice_id;
        $this->session->set_userdata(['company_id' => $companyId]);

        $this->load->model([
            'invoices/Mdl_items',
            'invoices/Mdl_received_inv_alert',
            'invoices/Mdl_invoice_tax_rates',
            'Mdl_tax_rates',
            'Mdl_payment_methods',
            'Mdl_custom_fields',
            'Mdl_item_lookups',
            'Mdl_companies',
            'invoices/Mdl_invoices_suppliers'
        ]);

        $invoice_custom = $this->Mdl_custom_fields->by_table('ip_invoice_custom', $invoiceId);
        if ($invoice_custom->num_rows()) {
            $invoice_custom = $invoice_custom->result_array();
            foreach ($invoice_custom as $key => $val) {
                $this->Mdl_invoices->set_form_value('custom[' . $val['custom_field_column'] . ']', $val['value_data']);
            }
        }

        $invoiceIsRecieved = $this->Mdl_invoices->check_if_received($invoiceId);
        if ($invoiceIsRecieved) {
            $invoice = $this->Mdl_invoices_suppliers->get_by_id($invoiceId);
        } else {
            $invoice = $this->Mdl_invoices->get_by_id($invoiceId);
        }

        $senderCompany = null;

        if ($invoiceIsRecieved) {
            $senderCompany = $this->Mdl_companies->get_array_by_id($invoice->company_id);
        }

        $invoiceItems = $this->db
            ->join('ip_products', 'ip_invoice_items.item_product_id = ip_products.product_id', 'left')
            ->get_where('ip_invoice_items', ['invoice_id' => $invoiceId])
            ->result_array();

        foreach ($invoiceItems as $it) {
            $taxes[] = $this->db
                ->get_where('ip_invoice_item_amounts', ['item_id' => $it['item_id']])
                ->row('item_tax_total');
        }

        $total_tax_amount = array_sum($taxes);

        $taxRates = $this->Mdl_tax_rates
            ->filter_where('ip_tax_rates.company_id', $companyId)
            ->get()
            ->result();

        $paymentMethods = $this->Mdl_payment_methods
            ->filter_where('ip_payment_methods.company_id', $companyId)
            ->get()
            ->result();

        $this->layout->set([
            'invoice'             => $invoice,
            'saved'               => isset($_GET['saved']) ? true : false,
            'items'               => $this->Mdl_items->where('invoice_id', $invoiceId)->get()->result(),
            'invoice_id'          => $invoiceId,
            'tax_rates'           => $taxRates,
            'invoice_tax_rates'   => $total_tax_amount,
            'payment_methods'     => $paymentMethods,
            'custom_fields'       => $this->Mdl_custom_fields->by_table('ip_invoice_custom')->result(),
            'custom_js_vars'      => [
                'currency_symbol'           => $this->Mdl_settings->setting('currency_symbol'),
                'currency_symbol_placement' => $this->Mdl_settings->setting('currency_symbol_placement'),
                'decimal_point'             => $this->Mdl_settings->setting('decimal_point')
            ],
            'item_lookups'        => $this->Mdl_item_lookups->get()->result(),
            'invoice_statuses'    => $this->Mdl_invoices->statuses(),
            'invoice_is_recieved' => $invoiceIsRecieved,
            'sender_company'      => $senderCompany,
            'old_items'           => $this->Mdl_items->old_items(),
            'new'                 => 1,
            'companyHash'         => $companyHash,
            'invoiceHash'         => $invoiceHash
        ]);

        $this->layout->buffer([
            ['modal_delete_invoice', 'invoices/modal_delete_invoice'],
            ['modal_add_invoice_tax', 'invoices/modal_add_invoice_tax'],
            ['modal_add_payment', 'payments/modal_add_payment'],
            [ 'content', 'guest/guest_invoice_edit']
        ]);

        $this->layout->render('layout_guest2');
    }

    public function save($companyHash, $invoiceHash)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $companies = $this->Mdl_users->get()->result();
        if (!$companies) {
            show_404();
        }

        $companyInfo = $this->Mdl_users
            ->where('md5(ip_users.company_id)', $companyHash)
            ->get()
            ->row();

        if (!$companyInfo) {
            show_error('Bad company provided');
        }

        $invoiceInfo = $this->Mdl_invoices
            ->where('ip_invoices.company_id', $companyInfo->company_id)
            ->where('md5(ip_invoices.invoice_id)', $invoiceHash)
            ->get()
            ->row();

        if (!$invoiceInfo) {
            show_error('Bad invoice provided');
        }

        $companyId = $companyInfo->company_id;
        $invoiceId = $invoiceInfo->invoice_id;
        $this->session->set_userdata(['company_id' => $companyId]);

        $this->load->model([
            'invoices/Mdl_items',
            'invoices/Mdl_invoices',
            'Mdl_products',
            'Mdl_item_lookups',
            'Mdl_stock_alert',
            'invoices/Mdl_received_inv_alert',
            'suppliers/Mdl_suppliers'
        ]);

        $invoice_id = $this->input->post('invoice_id', true);
        $_POST['company_id'] = $this->session->userdata('company_id');
        $this->Mdl_invoices->set_id($invoiceId);

        $supplierId = $this->input->post('supplier_id', true);
        $supplierName = $this->input->post('supplier_name', true);
        if (!$supplierId && !$supplierName) {
            die(json_encode([
                'success'           => 0,
                    'validation_errors' => [
                        'supplier_name' => form_error('supplier_name', '', '')
                    ]
            ]));
        }

        if (!$supplierId && $supplierName) {
            $supplierId = $this->Mdl_suppliers->supplier_lookup($supplierName);
            $this->Mdl_suppliers->save($supplierId, [
                'supplier_reg_number' => $this->input->post('supplier_reg_number'),
                'supplier_address_1'  => $this->input->post('supplier_address'),
                'supplier_vat_id'     => $this->input->post('supplier_vat'),
                'supplier_phone'      => $this->input->post('supplier_phone')
            ]);
            $this->Mdl_invoices->save($invoiceId, ['supplier_id' => $supplierId]);
        }

        $items = json_decode($this->input->post('items', true));

        foreach ($items as $item) {
            // Check if an item has either a quantity + price or name or description

            if (
                !empty($item->item_quantity) &&
                !empty($item->item_price) ||
                !empty($item->item_name) ||
                !empty($item->item_description)
            ) {
                $item->item_quantity = round(floatval($item->item_quantity), 8);
                $item->item_price = round(floatval($item->item_price), 8);
                $item->item_discount_amount = floatval($item->item_discount_amount);
                $item_id = ($item->item_id) ?: null;

                if (
                    $item->new_row_added == 1 &&
                    $this->input->post('is_received', true) == 0 &&
                    $item->item_product_id < 0
                ) {
                    $productId = $this->Mdl_products->findProductIdBySearchString($companyId, $item->item_name);
                    if ($productId) {
                        $item->item_product_id = $productId;
                    } else {
                        $product['company_id'] = $companyId;
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

                $this->Mdl_items->save($invoice_id, $item_id, $item);
            } else {
                $this->load->library('form_validation');
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

        $db_array = [
            'invoice_terms'            => $this->input->post('invoice_terms', true),
            'invoice_discount_amount'  => $this->input->post('invoice_discount_amount', true),
            'invoice_discount_percent' => $this->input->post('invoice_discount_percent', true),
            'is_received'              => 1
        ];

        $this->Mdl_invoices->save($invoice_id, $db_array);

        $this->load->model('invoices/Mdl_invoice_amounts');
        $this->Mdl_invoice_amounts->calculate($invoice_id);
        $response = ['success' => 1];
        $this->session->set_flashdata('alert_success', lang('invoice_saved'));

        echo json_encode($response);
    }

    /**
     * Track invoice
     * @param $invoiceId
     */
    public function invoicetrk($invoiceId)
    {
        $invoiceId = intval($invoiceId);
        $this->Mdl_invoices->save($invoiceId, [
            'invoice_status_id' => 3,
            'is_rec_and_seen'   => 1
        ]);

        $filePath = 'assets/responsive/img/ti.png';

        header("Content-Type: image/png");
        header("Content-Length: ", filesize($filePath));
        header('Expires: 0');
        readfile($filePath);
    }

    /**
     * Track quote
     * @param $quoteId
     */
    public function quotetrk($quoteId)
    {
        $quoteId = intval($quoteId);
        $this->Mdl_quotes->save($quoteId, [
            'quote_status_id' => 3
        ]);

        $filePath = 'assets/responsive/img/ti.png';

        header("Content-Type: image/png");
        header("Content-Length: ", filesize($filePath));
        header('Expires: 0');
        readfile($filePath);
    }
}
