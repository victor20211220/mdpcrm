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
            'Mdl_clients',
            'Mdl_payments',
            'invoices/Mdl_invoices',
            'quotes/Mdl_quotes'
        ]);
    }

    /**
     * Filter invoices
     */
    public function filter_invoices()
    {
        $query = $this->input->post('filter_query', true);

        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->Mdl_invoices->like("
                    CONCAT_WS(
                        '^', LOWER(invoice_number), invoice_date_created,
                        invoice_date_due, LOWER(client_name),
                        invoice_total, invoice_balance
                    )",
                    $keyword
                );
            }
        }

        $this->layout->load_view('invoices/partial_invoice_table', [
            'invoices'         => $this->Mdl_invoices->get()->result(),
            'invoice_statuses' => $this->Mdl_invoices->statuses()
        ]);
    }

    /**
     * Filter quotes
     */
    public function filter_quotes()
    {
        $query = $this->input->post('filter_query', true);

        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->Mdl_quotes->like("
                    CONCAT_WS(
                        '^', LOWER(quote_number), quote_date_created,
                        quote_date_expires, LOWER(client_name), quote_total
                    )",
                    $keyword
                );
            }
        }

        $this->layout->load_view('quotes/partial_quote_table', [
            'quotes'         => $this->Mdl_quotes->get()->result(),
            'quote_statuses' => $this->Mdl_quotes->statuses()
        ]);
    }

    /**
     * Filter clients
     */
    public function filter_clients()
    {
        $query = $this->input->post('filter_query', true);

        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->Mdl_clients->like("
                    CONCAT_WS(
                        '^', LOWER(client_name), LOWER(client_email),
                        client_phone, client_active
                    )",
                    $keyword
                );
            }
        }

        $this->layout->load_view('clients/partial_client_table', [
            'records' => $this->Mdl_clients->with_total_balance()->get()->result()
        ]);
    }

    /**
     * Filter payments
     */
    public function filter_payments()
    {
        $query = $this->input->post('filter_query', true);

        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->Mdl_payments->like("
                    CONCAT_WS(
                        '^', payment_date, LOWER(invoice_number),
                        LOWER(client_name), payment_amount, LOWER(payment_method_name),
                        LOWER(payment_note)
                    )",
                    $keyword
                );
            }
        }

        $this->layout->load_view('payments/partial_payment_table', [
            'payments' => $this->Mdl_payments->get()->result()
        ]);
    }
}
