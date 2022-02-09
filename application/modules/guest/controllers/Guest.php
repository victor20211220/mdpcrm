<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guest extends Guest_Controller
{
    /**
     * Guest constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'quotes/Mdl_quotes',
            'invoices/Mdl_invoices'
        ]);
    }

    /**
     * Index controller
     */
    public function index()
    {
        $overdueInvoices = $this->Mdl_invoices
            ->is_overdue()
            ->where_in('ip_invoices.client_id', $this->user_clients)
            ->get()
            ->result();

        $openQuotes = $this->Mdl_quotes
            ->is_open()
            ->where_in('ip_quotes.client_id', $this->user_clients)
            ->get()
            ->result();

        $openInvoices = $this->Mdl_invoices
            ->is_open()
            ->where_in('ip_invoices.client_id', $this->user_clients)
            ->get()
            ->result();

        $this->layout->set([
            'overdue_invoices' => $overdueInvoices,
            'open_quotes'      => $openQuotes,
            'open_invoices'    => $openInvoices
        ]);

        $this->layout->buffer('content', 'guest/index');
        $this->layout->render('layout_guest');
    }
}
