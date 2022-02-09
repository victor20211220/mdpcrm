<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    /**
     * Ajax constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->module('layout');
        $this->load->model([
            'Mdl_payments',
            'Mdl_payment_methods'
        ]);
    }

    /**
     * Add payment
     */
    public function add()
    {
        $_POST['company_id'] = $this->companyId;
        if ($this->Mdl_payments->run_validation()) {
            $this->Mdl_payments->save();

            $response = ['success' => 1];
        } else {
            $response = [
                'success'           => 0,
                'validation_errors' => json_errors()
            ];
        }

        echo json_encode($response);
    }

    /**
     * Modal add payment
     */
    public function modal_add_payment()
    {
        $paymentMethods = $this->Mdl_payment_methods
            ->filter_where('ip_payment_methods.company_id', $this->companyId)
            ->get()
            ->result();

        $data = [
            'payment_methods'        => $paymentMethods,
            'invoice_id'             => $this->input->post('invoice_id', true),
            'invoice_balance'        => $this->input->post('invoice_balance', true),
            'invoice_payment_method' => $this->input->post('invoice_payment_method', true)
        ];

        $this->layout->load_view('payments/modal_add_payment', $data);
    }
}
