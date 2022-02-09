<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Quotes
 */
class Quotes extends Admin_Controller
{
    /**
     * Quotes constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mdl_quotes');
    }

    /**
     * Display all quotes by default
     */
    public function index()
    {
        redirect('quotes/status/all');
    }

    /**
     * Show by status
     * @param string $status
     * @param int $page
     */
    public function status($status = 'all', $page = 0)
    {
        switch ($status) {
            case 'draft' :
                $this->Mdl_quotes->is_draft();
                break;
            case 'sent' :
                $this->Mdl_quotes->is_sent();
                break;
            case 'viewed' :
                $this->Mdl_quotes->is_viewed();
                break;
            case 'approved' :
                $this->Mdl_quotes->is_approved();
                break;
            case 'rejected' :
                $this->Mdl_quotes->is_rejected();
                break;
            case 'canceled' :
                $this->Mdl_quotes->is_canceled();
                break;
        }

        $this->Mdl_quotes->filter_where('ip_quotes.company_id', $this->companyId);
        $this->Mdl_quotes->paginate(site_url('quotes/status/' . $status), $page);
        $quotes = $this->Mdl_quotes->result();

        $this->layout->set([
            'quotes'             => $quotes,
            'status'             => $status,
            'filter_display'     => true,
            'filter_placeholder' => lang('filter_quotes'),
            'filter_method'      => 'filter_quotes',
            'quote_statuses'     => $this->Mdl_quotes->statuses()
        ]);

        $this->layout->buffer('content', 'quotes/index');
        $this->layout->render();
    }

    /**
     * View quote
     * @param $quote_id
     */
    public function view($quote_id)
    {
        $this->load->model('Mdl_quote_items');
        $this->load->model('Mdl_tax_rates');
        $this->load->model('Mdl_quote_tax_rates');
        $this->load->model('Mdl_custom_fields');
        $this->load->library('encrypt');

        $quote_custom = $this->Mdl_custom_fields->by_table('ip_quote_custom', $quote_id);

        if ($quote_custom->num_rows()) {
            $quote_custom = $quote_custom->result_array();

            foreach ($quote_custom as $key => $val) {
                $this->Mdl_quotes->set_form_value('custom[' . $val['custom_field_column'] . ']', $val['value_data']);

            }
        }

        $quote = $this->Mdl_quotes->get_by_id($quote_id);

        if ($quote_id && $quote->company_id != $this->session->userdata('company_id')) {
            show_404();
        }

        if (!$quote) {
            show_404();
        }

        $tax_rates = $this->Mdl_tax_rates->filter_where('ip_tax_rates.company_id',
            $this->session->userdata('company_id'))->get()->result();

        $this->layout->set([
            'quote'           => $quote,
            'items'           => $this->Mdl_quote_items->where('quote_id', $quote_id)->get()->result(),
            'quote_id'        => $quote_id,
            'tax_rates'       => $tax_rates, //$this->Mdl_tax_rates->get()->result(),
            'quote_tax_rates' => $this->Mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result(),
            'custom_fields'   => $this->Mdl_custom_fields->by_table('ip_quote_custom')->result(),
            'custom_js_vars'  => [
                'currency_symbol'           => $this->Mdl_settings->setting('currency_symbol'),
                'currency_symbol_placement' => $this->Mdl_settings->setting('currency_symbol_placement'),
                'decimal_point'             => $this->Mdl_settings->setting('decimal_point')
            ],
            'quote_statuses'  => $this->Mdl_quotes->statuses()
        ]);

        $this->layout->buffer([
            [
                'modal_delete_quote',
                'quotes/modal_delete_quote'
            ],
            [
                'modal_add_quote_tax',
                'quotes/modal_add_quote_tax'
            ],
            [
                'content',
                'quotes/view'
            ]
        ]);

        $this->layout->render();
    }

    /**
     * Delete
     * @param $quote_id
     */
    public function delete($quote_id)
    {
        $this->Mdl_quotes->delete($quote_id);
        redirect('quotes/index');
    }

    /**
     * Delete item
     * @param $quoteId
     * @param $itemId
     */
    public function delete_item($quoteId, $itemId)
    {
        $this->load->model('Mdl_quote_items');
        $this->Mdl_quote_items->delete($itemId);

        redirect('quotes/view/' . $quoteId);
    }

    /**
     * Generate pdf
     * @param $quoteId
     * @param bool $stream
     * @param null $quoteTemplate
     */
    public function generate_pdf($quoteId, $stream = true, $quoteTemplate = null)
    {
        $quoteTemplate = $this->db->get_where('ip_settings', [
            'company_id'  => $this->companyId,
            'setting_key' => 'pdf_invoice_template'
        ])->row('setting_value');

        $quote = $this->Mdl_quotes->get_by_id($quoteId);

        if ($quoteId && $quote->company_id != $this->companyId) {
            show_404();
        }

        $this->load->helper('pdf');

        if ($this->Mdl_settings->setting('mark_quotes_sent_pdf') == 1) {
            $this->Mdl_quotes->mark_sent($quoteId);
        }

        generate_quote_pdf($quoteId, $stream, $quoteTemplate);
    }

    /**
     * Delete quote tax
     * @param $quote_id
     * @param $quote_tax_rate_id
     */
    public function delete_quote_tax($quote_id, $quote_tax_rate_id)
    {
        $this->load->model('Mdl_quote_tax_rates');
        $this->Mdl_quote_tax_rates->delete($quote_tax_rate_id);

        $this->load->model('Mdl_quote_amounts');
        $this->Mdl_quote_amounts->calculate($quote_id);

        redirect('quotes/view/' . $quote_id);
    }

    /**
     * Recalculate all quotes
     */
    public function recalculate_all_quotes()
    {
        $this->db->select('quote_id');
        $quote_ids = $this->db->get('ip_quotes')->result();

        $this->load->model('Mdl_quote_amounts');

        foreach ($quote_ids as $quote_id) {
            $this->Mdl_quote_amounts->calculate($quote_id->quote_id);
        }
    }
}
