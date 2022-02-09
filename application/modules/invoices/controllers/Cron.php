<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends Base_Controller
{
    public function recur()
    {
        $this->load->model([
            'invoices/Mdl_invoices_recurring',
            'invoices/Mdl_invoices',
            'Mdl_email_templates'
        ]);
        $this->load->helper('mailer');

        $invoicesRecurring = $this->Mdl_invoices_recurring->active()->get()->result();

        foreach ($invoicesRecurring as $invoiceRecurring) {
            $sourceId = $invoiceRecurring->invoice_id;
            $invoice = $this->Mdl_invoices->get_by_id($sourceId);

            $targetId = $this->Mdl_invoices->create([
                'client_id'            => $invoice->client_id,
                'company_id'           => $invoice->company_id,
                'invoice_date_created' => $invoiceRecurring->recur_next_date,
                'invoice_date_due'     => $this->Mdl_invoices->get_date_due($invoiceRecurring->recur_next_date),
                'invoice_group_id'     => $invoice->invoice_group_id,
                'user_id'              => $invoice->user_id,
                'invoice_number'       => $this->Mdl_invoices->get_invoice_number($invoice->invoice_group_id),
                'invoice_url_key'      => $this->Mdl_invoices->get_url_key(),
                'invoice_terms'        => $invoice->invoice_terms
            ], false);

            $this->Mdl_invoices->copy_invoice($sourceId, $targetId);
            $this->Mdl_invoices_recurring->set_next_recur_date($invoiceRecurring->invoice_recurring_id);

            if ($this->Mdl_settings->setting('automatic_email_on_recur') and mailer_configured()) {
                $newInvoice = $this->Mdl_invoices->get_by_id($targetId);

                $emailTemplateId = $this->Mdl_settings->setting('email_invoice_template');
                if (!$emailTemplateId) {
                    return;
                }

                $emailTemplate = $this->Mdl_email_templates->where('email_template_id', $emailTemplateId)->get();
                if ($emailTemplate->num_rows() == 0) {
                    return;
                }

                $tpl = $emailTemplate->row();

                $from = !empty($tpl->email_template_from_email) ?
                    [$tpl->email_template_from_email, $tpl->email_template_from_name] :
                    [$invoice->user_email, ""];

                $subject = !empty($tpl->email_template_subject) ?
                    $tpl->email_template_subject :
                    lang('invoice') . ' #' . $newInvoice->invoice_number;

                email_invoice(
                    $targetId, $tpl->email_template_pdf_template, $from, $invoice->client_email, $subject,
                    $tpl->email_template_body, $tpl->email_template_cc, $tpl->email_template_bcc
                );

                $this->Mdl_invoices->mark_sent($targetId);
            }
        }
    }
}
