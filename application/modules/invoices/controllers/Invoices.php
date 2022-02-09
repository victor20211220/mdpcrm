<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices extends Admin_Controller
{
    /**
     * Invoices constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_items',
            'Mdl_tax_rates',
            'Mdl_payment_methods',
            'Mdl_invoice_tax_rates',
            'Mdl_invoices_suppliers',
            'Mdl_invoice_amounts',
            'Mdl_custom_fields',
            'Mdl_item_lookups',
            'Mdl_companies',
            'Mdl_invoices',
            'Mdl_received_inv_alert'
        ]);
    }

    /**
     * Index action
     */
    public function index()
    {
        redirect('invoices/status/all');
    }

    /**
     * Get by status
     * @param string $status
     * @param int $page
     */
    public function status($status = 'all', $page = 0)
    {
        // Determine which group of invoices to load
        switch ($status) {
            case 'draft' :
                $this->Mdl_invoices->is_draft();
                break;
            case 'sent' :
                $this->Mdl_invoices->is_sent();
                break;
            case 'viewed' :
                $this->Mdl_invoices->is_viewed();
                break;
            case 'paid' :
                $this->Mdl_invoices->is_paid();
                break;
            case 'overdue' :
                $this->Mdl_invoices->is_overdue();
                break;
        }

        $invoices = $this->Mdl_invoices
            ->filter_where('ip_invoices.company_id', $this->companyId)
            ->get()
            ->result();

        $this->layout->set([
            'invoices'           => $invoices,
            'status'             => $status,
            'filter_display'     => true,
            'filter_placeholder' => lang('filter_invoices'),
            'filter_method'      => 'filter_invoices',
            'invoice_statuses'   => $this->Mdl_invoices->statuses()
        ]);

        $this->layout->buffer('content', 'invoices/index');
        $this->layout->render();
    }

    /**
     * Archive invoices
     */
    public function archive()
    {
        $invoiceArray = [];
        if (isset($_POST['invoice_number'])) {
            $invoiceNumber = $_POST['invoice_number'];
            $invoiceArray = glob('./uploads/archive/*' . '_' . $invoiceNumber . '.pdf');
            $this->layout->set(['invoices_archive' => $invoiceArray]);
            $this->layout->buffer('content', 'invoices/archive');
            $this->layout->render();
        } else {
            foreach (
                glob('./uploads/archive/*.pdf') as $file) {
                array_push($invoiceArray, $file);
            }

            rsort($invoiceArray);

            $this->layout->set(['invoices_archive' => $invoiceArray]);
            $this->layout->buffer('content', 'invoices/archive');
            $this->layout->render();
        }
    }

    /**
     * Download invoice
     * @param $invoice
     */
    public function download($invoice)
    {
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $invoice . '"');
        readfile('./uploads/archive/' . $invoice);
    }

    /**
     * View invoice
     * @param $invoiceId
     * @param bool $new
     */
    public function view($invoiceId, $new = false)
    {
        $this->load->module('payments');

        $invoiceCustom = $this->Mdl_custom_fields->by_table('ip_invoice_custom', $invoiceId);
        if ($invoiceCustom->num_rows()) {
            $invoiceCustom = $invoiceCustom->result_array();
            foreach ($invoiceCustom as $key => $val) {
                $this->Mdl_invoices->set_form_value('custom[' . $val['custom_field_column'] . ']', $val['value_data']);
            }
        }

        $invoiceIsReceived = $this->Mdl_invoices->check_if_received($invoiceId);
        if ($invoiceIsReceived) {
            $invoice = $this->Mdl_invoices_suppliers->get_by_id($invoiceId);
        } else {
            $invoice = $this->Mdl_invoices->get_by_id($invoiceId);
        }

        $senderCompany = null;
        if ($invoiceIsReceived) {
            $senderCompany = $this->Mdl_companies->get_array_by_id($invoice->company_id);

            if ($invoice->is_received == 0) {
                $this->Mdl_invoices->mark_as_rec_seen($invoiceId);
                $this->Mdl_received_inv_alert->check_alert_on_my_comp();
            }
        }

        if (
            $invoice == false or
            (
                $invoice->company_id != 0 AND $invoice->company_id != $this->session->userdata('company_id')
            )
            AND $invoiceIsReceived == false
        ) {
            show_404();
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

        $totalTaxAmount = array_sum($taxes);
        $taxRates = $this->Mdl_tax_rates->filter_where('ip_tax_rates.company_id', $this->companyId)->get()->result();

        $paymentMethods = $this->Mdl_payment_methods
            ->filter_where('ip_payment_methods.company_id', $this->companyId)
            ->get()
            ->result();

        $this->layout->set([
            'invoice'             => $invoice,
            'saved'               => isset($_GET['saved']) ? true : false,
            'items'               => $this->Mdl_items->where('invoice_id', $invoiceId)->get()->result(),
            'invoice_id'          => $invoiceId,
            'tax_rates'           => $taxRates,
            'invoice_tax_rates'   => $totalTaxAmount,
            'payment_methods'     => $paymentMethods,
            'custom_fields'       => $this->Mdl_custom_fields->by_table('ip_invoice_custom')->result(),
            'custom_js_vars'      => [
                'currency_symbol'           => $this->Mdl_settings->setting('currency_symbol'),
                'currency_symbol_placement' => $this->Mdl_settings->setting('currency_symbol_placement'),
                'decimal_point'             => $this->Mdl_settings->setting('decimal_point')
            ],
            'item_lookups'        => $this->Mdl_item_lookups->get()->result(),
            'invoice_statuses'    => $this->Mdl_invoices->statuses(),
            'invoice_is_recieved' => $invoiceIsReceived,
            'sender_company'      => $senderCompany,
            'old_items'           => $this->Mdl_items->old_items(),
            'new'                 => $new
        ]);

        $this->layout->buffer([
            ['modal_delete_invoice', 'invoices/modal_delete_invoice'],
            ['modal_add_invoice_tax', 'invoices/modal_add_invoice_tax'],
            ['modal_add_payment', 'payments/modal_add_payment'],
            [ 'content', 'invoices/view']
        ]);

        $this->layout->render();
    }

    /**
     * Show list of received invoices
     */
    public function received()
    {
        $this->Mdl_invoices->is_received();
        $invoices = $this->Mdl_invoices->get()->result();

        $this->layout->set([
            'invoices'           => $invoices,
            'filter_display'     => true,
            'filter_placeholder' => lang('filter_invoices'),
            'filter_method'      => 'filter_invoices'
        ]);

        $this->layout->buffer('content', 'invoices/received');
        $this->layout->render();
    }

    /**
     * Delete invoice
     * @param $invoiceId
     */
    public function delete($invoiceId)
    {
        $invoice = $this->Mdl_invoices->get_by_id($invoiceId);
        $invoice_status = $invoice->invoice_status_id;

        if ($invoice_status == 1 || $this->config->item('enable_invoice_deletion') === true) {
            $this->Mdl_invoices->delete($invoiceId);
        } else {
            $this->session->set_flashdata('alert_error', lang('invoice_deletion_forbidden'));
        }

        redirect('invoices/index');
    }

    /**
     * Delete invoice item
     * @param $invoiceId
     * @param $itemId
     */
    public function delete_item($invoiceId, $itemId)
    {
        $this->Mdl_items->delete($itemId);
        redirect('invoices/view/' . $invoiceId);
    }

    /**
     * Generate pdf
     * @param $invoiceId
     * @param bool $stream
     * @param null $invoiceTemplate
     */
    public function generate_pdf($invoiceId, $stream = true, $invoiceTemplate = null)
    {
        $this->load->helper('pdf');

        $invoiceTemplate = $this->db->get_where('ip_settings', [
            'company_id'  => $this->session->userdata('company_id'),
            'setting_key' => 'pdf_invoice_template'
        ])->row('setting_value');

        $invoice = $this->Mdl_invoices->get_by_id($invoiceId);

        if (
            $invoiceId &&
            (
                $invoice->company_id != $this->session->userdata('company_id') &&
                $this->Mdl_invoices->check_if_received($invoiceId) == false
            )
        ) {
            show_404();
        }

        if ($this->Mdl_settings->setting('mark_invoices_sent_pdf') == 1) {
            $this->Mdl_invoices->mark_sent($invoiceId);
        }

        generate_invoice_pdf($invoiceId, $stream, $invoiceTemplate);
    }

    /**
     * Delete invoice tax
     * @param $invoice_id
     * @param $invoice_tax_rate_id
     */
    public function delete_invoice_tax($invoice_id, $invoice_tax_rate_id)
    {
        $this->Mdl_invoice_tax_rates->delete($invoice_tax_rate_id);
        $this->Mdl_invoice_amounts->calculate($invoice_id);

        redirect('invoices/view/' . $invoice_id);
    }

    /**
     * Recalculate all invoices
     */
    public function recalculate_all_invoices()
    {
        $this->db->select('invoice_id');
        $invoices = $this->db->get('ip_invoices')->result();

        foreach ($invoices as $invoice) {
            $this->Mdl_invoice_amounts->calculate($invoice->invoice_id);
        }
    }

    public function impexp()
    {
        if ($this->input->post('btn_export', true)) {
            $fromDate = strtotime($this->input->post('from_date', true));
            $toDate = strtotime($this->input->post('to_date', true));

            if ($fromDate == "") {
                $fromDate = "2015-01-01";
            } else {
                $fromDate = date('Y-m-d', $fromDate);
            }

            if ($toDate == "") {
                $toDate = date("Y-m-d", time());
            } else {
                $toDate = date('Y-m-d', $toDate);
            }

            $invoices = $this->Mdl_invoices
                ->where('ip_invoices.invoice_date_created >=', $fromDate)
                ->where('ip_invoices.invoice_date_created <=', $toDate)
                ->filter_where('ip_invoices.company_id', $this->companyId)
                ->filter_where('ip_invoices.is_received', 0)
                ->get()
                ->result();

            if ($this->input->post('export_type', true) == 'excel') {
                $params['invoices'] = $invoices;
                $params['export_type'] = 'excel';
                $params['new_world_order'] = explode(',', $this->input->post('new_world_order', true));
                $params['additional_options'] = $this->input->post('additional_options', true);
                $this->load->helper('excel');

                export_as_excel($params, $fromDate, $toDate);
            }

            if ($this->input->post('export_type', true) == 'csv') {
                $params['invoices'] = $invoices;
                $params['export_type'] = 'csv';
                $params['new_world_order'] = explode(',', $this->input->post('new_world_order', true));
                $params['additional_options'] = $this->input->post('additional_options', true);
                $this->load->helper('excel');

                export_as_excel($params, $fromDate, $toDate);
            }

            if ($this->input->post('export_type', true) == 'zip') {
                $params['invoices'] = $invoices;
                $params['export_type'] = 'zip_pdf';
                $this->load->helper('zip_pdf');

                export_as_zip_pdf($params, $fromDate, $toDate);
            }

            if ($this->input->post('export_type', true) == 'raw_xml') {
                $params['invoices'] = $invoices;
                $params['export_type'] = 'raw_xml';
                $params['new_world_order'] = explode(',', $this->input->post('new_world_order', true));
                $params['additional_options'] = $this->input->post('additional_options', true);
                $this->load->helper('raw_xml_invoice');

                $r = raw_xml_invoice($params, $fromDate, $toDate);
                if ($r == false) {
                    redirect('invoices/impexp');
                }
            }

            if ($this->input->post('export_type', true) == 'xml') {
                $params['invoices'] = $invoices;
                $params['export_type'] = 'xml';
                $this->load->helper('xml_invoice');
                $r = xml_invoice($params, $fromDate, $toDate);
                if ($r == false) {
                    redirect('invoices/impexp');
                }
            }

            if ($this->input->post('export_type', true) == 'rec_inv') {
                $params['export_type'] = 'rec_inv';
                $this->load->helper('xml_iso_20022_invoice');

                // Determine received invoices

                $invoices = $this->Mdl_invoices
                    ->is_received()
                    ->where('ip_invoices.invoice_date_created >=', $fromDate)
                    ->where('ip_invoices.invoice_date_created <=', $toDate)
                    ->get()
                    ->result();

                $params['invoices'] = $invoices;
                $r = xml_iso_20022_invoice($params, $fromDate, $toDate);

                if ($r == false) {
                    redirect('invoices/impexp');
                }
            }
        }

        $this->layout->set(['mysql_cols' => $this->Mdl_invoices->get_cols_name_for_export()]);
        $this->layout->buffer('content', 'invoices/impexp_invoice');
        $this->layout->render();
    }
}
