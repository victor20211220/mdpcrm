<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payments extends Guest_Controller
{
    /**
     * Payments constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mdl_payments');
    }

    /**
     * Index controller
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->Mdl_payments->where('
            (ip_payments.invoice_id IN
                (
                    SELECT invoice_id
                    FROM ip_invoices
                    WHERE client_id IN (' . implode(',', $this->user_clients) . ')
                )
            )'
        );
        $this->Mdl_payments->paginate(site_url('guest/payments/index'), $page);
        $payments = $this->Mdl_payments->result();

        $this->layout->set([
            'payments'           => $payments,
            'filter_display'     => true,
            'filter_placeholder' => lang('filter_payments'),
            'filter_method'      => 'filter_payments'
        ]);

        $this->layout->buffer('content', 'guest/payments_index');
        $this->layout->render('layout_guest');
    }
}
