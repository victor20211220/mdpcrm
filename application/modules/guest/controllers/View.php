<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class View extends Base_Controller
{
    /**
     * View constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'invoices/Mdl_items',
            'invoices/Mdl_invoices',
            'invoices/Mdl_invoice_tax_rates',
            'quotes/Mdl_quotes',
            'quotes/Mdl_quote_items',
            'quotes/Mdl_quote_tax_rates',
            'Mdl_payment_methods'
        ]);

        $this->load->helper([
            'pdf',
            'mailer'
        ]);
    }

    /**
     * View invoice
     * @param $urlKey
     */
    public function invoice($urlKey)
    {
        $invoice = $this->Mdl_invoices->where('invoice_url_key', $urlKey)->get();
        if ($invoice->num_rows() != 1) {
            show_404();
        }

        $invoice = $invoice->row();

        if ($this->session->userdata('user_type') != 1 and $invoice->invoice_status_id == 2) {
            $this->Mdl_invoices->mark_viewed($invoice->invoice_id);
        }

        $paymentMethod = $this->Mdl_payment_methods
            ->where('payment_method_id', $invoice->payment_method)
            ->get()
            ->row();

        if ($invoice->payment_method == 0) {
            $paymentMethod = null;
        }

        $invoiceItems = $this->Mdl_items
            ->where('invoice_id', $invoice->invoice_id)
            ->get()
            ->result();

        $invoiceTaxRates = $this->Mdl_invoice_tax_rates
            ->where('invoice_id', $invoice->invoice_id)
            ->get()
            ->result();

        $itemsDiscount = false;
        foreach ($invoiceItems as $i) {
            if ($i->item_discount_percent > 0) {
                $itemsDiscount = true;
                break;
            }
        }

        $data = [
            'invoice'           => $invoice,
            'items'             => $invoiceItems,
            'itemsDiscount'     => $itemsDiscount,
            'invoice_tax_rates' => $invoiceTaxRates,
            'invoice_url_key'   => $urlKey,
            'flash_message'     => $this->session->flashdata('flash_message'),
            'payment_method'    => $paymentMethod
        ];

        if ($this->Mdl_settings->setting('public_invoice_template') != '') {
            $template = $this->Mdl_settings->setting('public_invoice_template');
        } else {
            $template = 'default';
        }

        $this->load->view('invoice_templates/public/' . $template . '.php', $data);
    }

    /**
     * Generate invoice pdf
     * @param $urlKey
     * @param bool $stream
     * @param null $template
     */
    public function generate_invoice_pdf($urlKey, $stream = true, $template = null)
    {
        $invoice = $this->Mdl_invoices->where('invoice_url_key', $urlKey)->get();
        if ($invoice->num_rows() != 1) {
            show_404();
        }

        $invoice = $invoice->row();

        if (!$template) {
            if ($this->Mdl_settings->setting('public_invoice_template') != '') {
                $template = $this->Mdl_settings->setting('public_invoice_template');
            } else {
                $template = 'default';
            }
        }

        generate_invoice_pdf($invoice->invoice_id, $stream, $template, 1);
    }

    /**
     * View quote
     * @param $urlKey
     */
    public function quote($urlKey)
    {
        $quote = $this->Mdl_quotes->guest_visible()->where('quote_url_key', $urlKey)->get();
        if ($quote->num_rows() != 1) {
            show_404();
        }

        $quote = $quote->row();

        if ($this->session->userdata('user_type') != 1 and $quote->quote_status_id == 2) {
            $this->Mdl_quotes->mark_viewed($quote->quote_id);
        }

        $quoteItems = $this->Mdl_quote_items->where('quote_id', $quote->quote_id)->get()->result();
        $quoteTaxRates = $this->Mdl_quote_tax_rates->where('quote_id', $quote->quote_id)->get()->result();

        $data = [
            'quote'           => $quote,
            'items'           => $quoteItems,
            'quote_tax_rates' => $quoteTaxRates,
            'quote_url_key'   => $urlKey,
            'flash_message'   => $this->session->flashdata('flash_message')
        ];

        if ($this->Mdl_settings->setting('public_quote_template') != '') {
            $template = $this->Mdl_settings->setting('public_quote_template');
        } else {
            $template = 'default';
        }

        $this->load->view('quote_templates/public/' . $template . '.php', $data);
    }

    /**
     * Generate quote pdf
     * @param $urlKey
     * @param bool $stream
     * @param null $template
     */
    public function generate_quote_pdf($urlKey, $stream = true, $template = null)
    {
        $quote = $this->Mdl_quotes->guest_visible()->where('quote_url_key', $urlKey)->get();
        if ($quote->num_rows() != 1) {
            show_404();
        }

        $quote = $quote->row();

        if (!$template) {
            $template = $this->Mdl_settings->setting('default_pdf_quote_template');
        }

        generate_quote_pdf($quote->quote_id, $stream, $template);
    }

    /**
     * Approve quote
     * @param $urlKey
     */
    public function approve_quote($urlKey)
    {
        $quote = $this->Mdl_quotes->where('ip_quotes.quote_url_key', $urlKey)->get()->row();
        if (!$quote) {
            show_404();
        }

        $this->Mdl_quotes->approve_quote_by_key($urlKey);
        email_quote_status($quote->quote_id,"approved");

        redirect('guest/view/quote/' . $urlKey);
    }

    /**
     * Reject quote
     * @param $urlKey
     */
    public function reject_quote($urlKey)
    {
        $quote = $this->Mdl_quotes->where('ip_quotes.quote_url_key', $urlKey)->get()->row();
        if (!$quote) {
            show_404();
        }

        $this->Mdl_quotes->reject_quote_by_key($urlKey);
        email_quote_status($quote->quote_id, "rejected");

        redirect('guest/view/quote/' . $urlKey);
    }
}
