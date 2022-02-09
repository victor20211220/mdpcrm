<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payments extends Admin_Controller
{
    /**
     * Payments constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_payments',
            'invoices/Mdl_invoices',
            'invoices/Mdl_invoice_amounts',
            'Mdl_custom_fields',
            'Mdl_custom_fields_data',
            'Mdl_payment_methods'
        ]);
    }

    /**
     * Index controller
     * @param int $page
     */
    public function index($page = 0)
    {
        $invoices = $this->db->get_where('ip_payments', ['company_id' => $this->companyId])->result_array();
        $payments = $this->Mdl_payments->getPayments();

        $this->layout->set([
            'payments'           => $payments,
            'invoices'           => $invoices,
            'filter_display'     => true,
            'filter_placeholder' => lang('filter_payments'),
            'filter_method'      => 'filter_payments'
        ]);

        $this->layout->buffer('content', 'payments/index');
        $this->layout->render();
    }

    /**
     * Form
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel', true)) {
            redirect('payments');
        }

        if ($this->input->post('btn_submit', true)) {
            $_POST['company_id'] = $this->companyId;
            if ($this->Mdl_payments->run_validation()) {
                $id = $this->Mdl_payments->save($id);

                $this->Mdl_custom_fields_data->save_custom(
                    $id, $this->input->post('custom', true), 'ip_payment_custom'
                );

                redirect('payments');
            }
        }

        if ($this->input->post('btn_submit', true) == false) {
            $prep_form = $this->Mdl_payments->prep_form($id);

            if ($id and !$prep_form) {
                show_404();
            }

            $payment_custom = $this->Mdl_custom_fields->by_table('ip_payment_custom', $id);

            if ($payment_custom->num_rows()) {
                $payment_custom = $payment_custom->result_array();

                unset($payment_custom->payment_id, $payment_custom->payment_custom_id);

                foreach ($payment_custom as $key => $val) {
                    $this->Mdl_payments->set_form_value(
                        'custom[' . $val['custom_field_column'] . ']', $val['value_data']
                    );
                }
            }
        } else {
            if ($this->input->post('custom', true)) {
                foreach ($this->input->post('custom', true) as $key => $val) {
                    $this->Mdl_payments->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        $openInvoices = $this->Mdl_invoices
            ->filter_where('ip_invoices.company_id', $this->companyId)
            ->filter_where('ip_invoices.is_received =', 0)
            ->where('ip_invoice_amounts.invoice_balance >', 0)
            ->get()
            ->result();

        $amounts = [];
        $invoicePaymentMethods = [];
        foreach ($openInvoices as $openInvoice) {
            $amounts['invoice' . $openInvoice->invoice_id] = format_amount($openInvoice->invoice_balance);
            $invoicePaymentMethods['invoice' . $openInvoice->invoice_id] = $openInvoice->payment_method;
        }

        $check = $this->Mdl_payments->getByPk($id);

        if ($id && $check->company_id != $this->companyId) {
            show_404();
        }

        $paymentMethods = $this->Mdl_payment_methods
            ->filter_where('ip_payment_methods.company_id', $this->companyId)
            ->get()->result();

        $this->layout->set([
            'payment_id'              => $id,
            'payment_methods'         => $paymentMethods,
            'open_invoices'           => $openInvoices,
            'custom_fields'           => $this->Mdl_custom_fields->by_table('ip_payment_custom')->result(),
            'amounts'                 => json_encode($amounts),
            'invoice_payment_methods' => json_encode($invoicePaymentMethods)
        ]);

        if ($id) {
            $this->layout->set('payment', $this->Mdl_payments->where('ip_payments.payment_id', $id)->get()->row());
        }

        $this->layout->buffer('content', 'payments/form');
        $this->layout->render();
    }

    public function delete($id)
    {
        $this->Mdl_payments->delete($id);
        redirect('payments');
    }

    public function import()
    {
        if ($this->input->post('btn_submit_1', true)) {

            if (empty($_FILES['fileUpload']['tmp_name'])) {
                $this->session->set_flashdata('alert_error', lang('error_select_file'));
                redirect('payments/import');
            }

            $ext = pathinfo($_FILES['fileUpload']['name'], PATHINFO_EXTENSION);
            if (strtolower($ext) != 'xml') {
                $this->session->set_flashdata('alert_error', lang('error_select_only_xml'));
                redirect('clients/import');
            }

            $target_path = $_FILES['fileUpload']['tmp_name'];

            $sepa_file = simplexml_load_file($target_path);


            $invoices = [];
            $error_alerts = '';
            $success_alerts = '';

            if (!isset($sepa_file->CstmrCdtTrfInitn->GrpHdr->NbOfTxs) || $sepa_file->CstmrCdtTrfInitn->GrpHdr->NbOfTxs == 0) {
                $error_alerts .= lang('error_no_inv_found') . '</br>';
            }


            if ($sepa_file->CstmrCdtTrfInitn->GrpHdr->NbOfTxs == 1) {
                $invoices[] = $sepa_file->CstmrCdtTrfInitn->PmtInf->CdtTrfTxInf;
            }


            if ($sepa_file->CstmrCdtTrfInitn->GrpHdr->NbOfTxs > 1) {
                foreach ($sepa_file->CstmrCdtTrfInitn->PmtInf->CdtTrfTxInf as $inv) {
                    $invoices[] = $inv;
                }
            }

            //let's parse some invoices ... yummy
            foreach ($invoices as $invoice) {
                $ech = $invoice->PmtId->EndToEndId . '/--';
                //get system invoice after invoice number
                $sys_invoice = $this->Mdl_invoices
                    ->filter_where('invoice_number', $invoice->PmtId->EndToEndId . '')
                    ->filter_where('ip_invoices.company_id', $this->companyId)
                    ->get();

                if ($sys_invoice->num_rows()) {
                    $sys_invoice = $sys_invoice->row();
                } else {
                    $error_alerts .= sprintf(lang('invoice_x_not_found'), $invoice->PmtId->EndToEndId) . '</br>';
                    continue;
                }

                //add payment for that invoice
                $payment_note = sprintf(lang('payment_note'), $invoice->Cdtr->Nm, $invoice->CdtrAcct->Id->IBAN,
                        $invoice->RmtInf->Ustrd) . '</br>';
                $amount = (float)(preg_replace('/[^0-9\.]/', '', $invoice->Amt->InstdAmt));

                $db_array = [
                    'invoice_id'        => $sys_invoice->invoice_id,
                    'company_id'        => $this->session->userdata('company_id'),
                    'payment_date'      => date('m/d/Y'),
                    'payment_amount'    => $amount,
                    'payment_method_id' => '',
                    'payment_note'      => $payment_note,
                    'payment_is_sepa'   => 1
                ];

                foreach ($db_array as $k => $arr) {
                    $_POST[$k] = $arr;
                }

                if ($this->Mdl_payments->run_validation()) {
                    $id = $this->Mdl_payments->save(null);
                    $success_alerts .= sprintf(lang('sepa_imp_success'), $amount, $invoice->PmtId->EndToEndId) . '</b>';
                }

            }

            $this->session->set_flashdata('alert_error', $error_alerts);
            $this->session->set_flashdata('alert_success', $success_alerts);

            redirect('payments/import');
        }

        $this->layout->buffer('content', 'payments/import_1');
        $this->layout->render();
    }


    public function import_iso_20022()
    {
        if ($this->input->post('btn_submit_1', true)) {
            if (empty($_FILES['fileUpload']['tmp_name'])) {
                $this->session->set_flashdata('alert_error', lang('error_select_file'));
                redirect('payments/import_iso_20022');
            }

            $ext = pathinfo($_FILES['fileUpload']['name'], PATHINFO_EXTENSION);
            if (strtolower($ext) != 'xml') {
                $this->session->set_flashdata('alert_error', lang('error_select_only_xml'));
                redirect('clients/import_iso_20022');
            }

            $target_path = $_FILES['fileUpload']['tmp_name'];

            $iso_file = simplexml_load_file($target_path);
            $namespaces = $iso_file->getNameSpaces(true);
            $iso_file = $iso_file->children($namespaces['ns2']);


            $invoices = [];
            $error_alerts = '';
            $success_alerts = '';

            $incoming_payment = [];

            if (!isset($iso_file->BkToCstmrStmt->Stmt->Ntry)) {
                $error_alerts .= lang('error_no_inv_found') . '</br>';
            }

            foreach ($iso_file->BkToCstmrStmt->Stmt->Ntry as $inv) {
                if ($inv->CdtDbtInd == 'CRDT') {
                    $aux['amount'] = (string)$inv->Amt;
                    $aux['client_name'] = (string)$inv->NtryDtls->TxDtls->RltdPties->Dbtr->Nm;
                    $aux['description'] = (string)$inv->NtryDtls->TxDtls->RmtInf->Ustrd;
                    $aux['IBAN'] = (string)$inv->NtryDtls->TxDtls->RltdPties->DbtrAcct->Id->IBAN;

                    $incoming_payment[] = $aux;
                }
            }

            $open_invoices = $this->Mdl_invoices
                ->filter_where('ip_invoices.company_id', $this->companyId)
                ->where('ip_invoice_amounts.invoice_balance >', 0)
                ->get()
                ->result();

            $this->layout->set([
                'payments' => $incoming_payment,
                'invoices' => $open_invoices
            ]);

            $this->layout->buffer('content', 'payments/import_2_iso_20022');
            $this->layout->render();

        } elseif ($this->input->post('btn_submit_2', true)) {
            //after step 2
            //let's parse some invoices ... yummy
            $input = count($this->input->post('client_name', true));
            $error_alerts = '';
            $success_alerts = '';

            $invoice_id_array = $this->input->post('invoice_id', true);
            $client_name_array = $this->input->post('client_name', true);
            $IBAN_array = $this->input->post('IBAN', true);
            $description_array = $this->input->post('description', true);
            $amount_array = $this->input->post('amount', true);

            unset($_POST);
            $_POST = [];

            //print_r($invoice_id_array);

            for ($i = 0; $i < $input; $i++) {
                //$ech = $invoice->PmtId->EndToEndId.'/--';
                //get system invoice after invoice number
                if ($invoice_id_array[$i] == -1) {
                    continue;
                }

                $sys_invoice = $this->Mdl_invoices->filter_where('ip_invoices.invoice_id', $invoice_id_array[$i])
                    ->filter_where('ip_invoices.company_id', $this->companyId)
                    ->get();
                if ($sys_invoice->num_rows()) {
                    $sys_invoice = $sys_invoice->row();
                } else {
                    $error_alerts .= sprintf(lang('invoice_x_not_found'), $invoice_id_array[$i]) . '</br>';
                    continue;
                }

                //add payment for that invoice  'Method: SEPA IMPORT&#10;Creditor name:%s&#10;Creditor IBAN:%s&#10;Payment Comment:%s'
                $payment_note = sprintf(lang('payment_note_iso'), $client_name_array[$i], $IBAN_array[$i],
                        $description_array[$i]) . '</br>';
                $amount = (float)(preg_replace('/[^0-9\.]/', '', $amount_array[$i]));

                $db_array = [
                    'invoice_id'        => $invoice_id_array[$i],
                    'company_id'        => $this->session->userdata('company_id'),
                    'payment_date'      => date('Y-m-d'),
                    'payment_amount'    => $amount,
                    'payment_method_id' => '',
                    'payment_note'      => $payment_note,
                    'payment_is_ISO'    => 1
                ];

                foreach ($db_array as $k => $arr) {
                    $_POST[$k] = $arr;
                }

                if ($this->Mdl_payments->run_validation()/*&&$this->Mdl_payments->validate_payment_amount(floatval($this->input->post('amount', true)[$i]))*/) {

                    $id = $this->Mdl_payments->save(null, $db_array);
                    $success_alerts .= sprintf(lang('sepa_imp_success'), $amount,
                            $sys_invoice->invoice_number) . '</b>';
                }

                $this->session->set_flashdata('alert_error', $error_alerts);
                $this->session->set_flashdata('alert_success', $success_alerts);
            }

            redirect('payments/import_iso_20022');
        } elseif ($this->input->post('btn_cancel', true)) {
            redirect('payments/import_iso_20022');
        } else {
            $this->layout->buffer('content', 'payments/import_1_iso_20022');
            $this->layout->render();
        }
    }

    public function select()
    {
        $this->layout->buffer('content', 'payments/select');
        $this->layout->render();
    }
}
