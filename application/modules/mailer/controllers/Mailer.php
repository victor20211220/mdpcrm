<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mailer extends Admin_Controller
{
    private $mailerConfigured;

    /**
     * Mailer constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper([
            'mailer',
            'template'
        ]);

        $this->load->model([
            'invoices/Mdl_invoices',
            'invoices/Mdl_templates',
            'Mdl_email_templates',
            'Mdl_uploads',
            'paysera/Mdl_payment_codes',
            'quotes/Mdl_quotes',
            'settings/Mdl_settings'
        ]);

        $this->mailerConfigured = mailer_configured();

        if ($this->mailerConfigured == false) {
            $this->layout->buffer('content', 'mailer/not_configured');
            $this->layout->render();
        }
    }

    /**
     * Send invoice
     * @param $invoiceId
     */
    public function invoice($invoiceId)
    {
        if (!$this->mailerConfigured) {
            return;
        }

        $invoice = $this->Mdl_invoices
            ->filter_where('ip_invoices.company_id', $this->companyId)
            ->where('ip_invoices.invoice_id', $invoiceId)
            ->get()
            ->row();

        $emailTemplateId = select_email_invoice_template($invoice);

        if ($emailTemplateId) {
            $email_template = $this->Mdl_email_templates
                ->filter_where('company_id', $this->companyId)
                ->where('email_template_id', $emailTemplateId)
                ->get();

            $this->layout->set('email_template', json_encode($email_template->row()));
        } else {
            $this->layout->set('email_template', '{}');
        }

        $emailTemplates = $this->Mdl_email_templates
            ->filter_where('company_id', $this->companyId)
            ->where('email_template_type', 'invoice')
            ->get()
            ->result();

        $this->layout
            ->set('selected_pdf_template', select_pdf_invoice_template($invoice))
            ->set('selected_email_template', $emailTemplateId)
            ->set('email_templates', $emailTemplates)
            ->set('invoice', $invoice)
            ->set('pdf_templates', $this->Mdl_templates->get_invoice_templates())
            ->buffer('content', 'mailer/invoice')
            ->render();
    }

    /**
     * Quote
     * @param $quoteId
     */
    public function quote($quoteId)
    {
        if (!$this->mailerConfigured) {
            return;
        }

        $emailTemplateId = $this->Mdl_settings->setting('email_quote_template');

        if ($emailTemplateId) {
            $email_template = $this->Mdl_email_templates
                ->filter_where('company_id', $this->companyId)
                ->where('email_template_id', $emailTemplateId)
                ->get();

            $this->layout->set('email_template', json_encode($email_template->row()));
        } else {
            $this->layout->set('email_template', '{}');
        }

        $emailTemplates = $this->Mdl_email_templates
            ->filter_where('company_id', $this->companyId)
            ->where('email_template_type', 'quote')
            ->get()
            ->result();

        $this->layout
            ->set('selected_pdf_template', $this->Mdl_settings->setting('pdf_quote_template'))
            ->set('selected_email_template', $emailTemplateId)
            ->set('email_templates', $emailTemplates)
            ->set('quote', $this->Mdl_quotes->where('ip_quotes.quote_id', $quoteId)->get()->row())
            ->set('pdf_templates', $this->Mdl_templates->get_quote_templates())
            ->buffer('content', 'mailer/quote')
            ->render();
    }

    public function send_invoice($invoiceId)
    {
        if ($this->input->post('btn_cancel', true)) {
            redirect('invoices');
        }

        if (!$this->mailerConfigured) {
            return;
        }

        $from = [
            $this->input->post('from_email', true),
            $this->input->post('from_name', true)
        ];

        $pdf_template = $this->input->post('pdf_template', true);
        $to = $this->input->post('to_email', true);
        $subject = $this->input->post('subject', true);

        if (strlen($this->input->post('body', true)) != strlen(strip_tags($this->input->post('body', true)))) {
            $body = htmlspecialchars_decode($this->input->post('body', true));
        } else {
            $body = htmlspecialchars_decode(nl2br($this->input->post('body', true)));
        }

        $canPayOnline = $this->Mdl_settings->setting('merchant_enabled');
        if (isset($canPayOnline) && $canPayOnline == 1) {
            $uniqueCode = md5(uniqid(rand(), true));

            $p = $uniqueCode;
            $q = $invoiceId;

            $link = base_url() . 'paysera/pay?p=' . $p . '&q=' . $q . '&e=' . $to;
            $body .= 'To pay the invoice please click the following link. <a href="' . $link . '"' . '>Go to paysera</a>';

            $this->Mdl_payment_codes->add_code($invoiceId, $uniqueCode);
        }

        $body .= "<br><br><img src='" . site_url("guest/invoices/invoicetrk/{$invoiceId}") . "'/>";

        $cc = $this->input->post('cc', true);
        $bcc = $this->input->post('bcc', true);
        $attachment_files = $this->Mdl_uploads->get_invoice_uploads($invoiceId);

        if (email_invoice($invoiceId, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files)) {
            $this->Mdl_invoices->mark_sent($invoiceId);
            $this->session->set_flashdata('alert_success', lang('email_successfully_sent'));
            redirect('dashboard');
        } else {
            redirect('mailer/invoice/' . $invoiceId);
        }
    }

    public function send_quote($quoteId)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('quotes');
        }

        if (!$this->mailerConfigured) {
            return;
        }

        $from = [
            $this->input->post('from_email', true),
            $this->input->post('from_name', true)
        ];

        $pdf_template = $this->input->post('pdf_template', true);
        $to = $this->input->post('to_email', true);
        $subject = $this->input->post('subject', true);
        if (strlen($this->input->post('body', true)) != strlen(strip_tags($this->input->post('body', true)))) {
            $body = htmlspecialchars_decode($this->input->post('body', true));
        } else {
            $body = htmlspecialchars_decode(nl2br($this->input->post('body', true)));
        }

        $body .= "<br><br><img src='" . site_url("guest/invoices/quotetrk/{$quoteId}") . "'/>";

        $cc = $this->input->post('cc', true);
        $bcc = $this->input->post('bcc', true);
        $attachment_files = $this->Mdl_uploads->get_quote_uploads($quoteId);
        if (email_quote($quoteId, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files)) {
            $this->Mdl_quotes->mark_sent($quoteId);
            $this->session->set_flashdata('alert_success', lang('email_successfully_sent'));
            redirect('dashboard');
        } else {
            redirect('mailer/quote/' . $quoteId);
        }
    }

}
