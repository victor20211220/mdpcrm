<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_handler extends Base_Controller
{
    /**
     * Payment_handler constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library([
            'merchant',
            'encrypt'
        ]);

        $this->load->model([
            'invoices/Mdl_invoices',
            'payments/Mdl_payments'
        ]);
    }

    /**
     * Make payment
     * @param $urlKey
     */
    public function make_payment($urlKey)
    {
        $invoice = $this->Mdl_invoices->where('invoice_url_key', $urlKey)->get();
        if ($invoice->num_rows() != 1) {
            show_404();
        }

        $invoice = $invoice->row();

        $this->merchant->load($this->Mdl_settings->setting('merchant_driver'));
        $this->merchant->initialize([
            'username'  => $this->Mdl_settings->setting('merchant_username'),
            'password'  => $this->encrypt->decode($this->Mdl_settings->setting('merchant_password')),
            'signature' => $this->Mdl_settings->setting('merchant_signature'),
            'test_mode' => ($this->Mdl_settings->setting('merchant_test_mode')) ? true : false
        ]);

        $params = [
            'description' => lang('invoice') . ' #' . $invoice->invoice_number,
            'amount'      => $invoice->invoice_balance,
            'currency'    => $this->Mdl_settings->setting('merchant_currency_code'),
            'return_url'  => site_url('guest/payment_handler/payment_return/' . $urlKey . '/r'),
            'cancel_url'  => site_url('guest/payment_handler/payment_cancel/' . $urlKey . '/c')
        ];

        $response = $this->merchant->purchase($params);

        if (!$response->success()) {
            $this->session->set_flashdata('flash_message', $response->message());
            redirect('guest/view/invoice/' . $urlKey);
        }
    }

    /**
     * Payment return
     * @param $urlKey
     */
    public function payment_return($urlKey)
    {
        if ($this->payment_validate($urlKey)) {
            $this->session->set_flashdata('flash_message', lang('merchant_payment_success'));

            $invoice = $this->Mdl_invoices->where('invoice_url_key', $urlKey)->get();
            if ($invoice->num_rows() == 1) {
                $invoice = $invoice->row();
                $paymentMethodId = ($this->Mdl_settings->setting('online_payment_method')) ?
                    $this->Mdl_settings->setting('online_payment_method') :
                    0;

                $this->Mdl_payments->save(null, [
                    'invoice_id'        => $invoice->invoice_id,
                    'payment_date'      => date('Y-m-d'),
                    'payment_amount'    => $invoice->invoice_balance,
                    'payment_method_id' => $paymentMethodId
                ]);
            }
        } else {
            $this->session->set_flashdata('flash_message', lang('merchant_payment_fail'));
        }

        redirect('guest/view/invoice/' . $urlKey);
    }

    /**
     * Payment validate
     * @param $urlKey
     * @return int
     */
    private function payment_validate($urlKey)
    {
        $invoice = $this->Mdl_invoices->where('invoice_url_key', $urlKey)->get();
        if ($invoice->num_rows() != 1) {
            return 0;
        }

        $invoice = $invoice->row();

        $this->merchant->load($this->Mdl_settings->setting('merchant_driver'));
        $this->merchant->initialize([
            'username'  => $this->Mdl_settings->setting('merchant_username'),
            'password'  => $this->encrypt->decode($this->Mdl_settings->setting('merchant_password')),
            'signature' => $this->Mdl_settings->setting('merchant_signature'),
            'test_mode' => ($this->Mdl_settings->setting('merchant_test_mode')) ? true : false
        ]);

        $params = [
            'amount'   => $invoice->invoice_balance,
            'currency' => $this->Mdl_settings->setting('merchant_currency_code')
        ];

        $response = $this->merchant->purchase_return($params);
        $merchantResponse = ($response->success()) ? 1 : 0;

        $this->db->insert('ip_merchant_responses', [
            'invoice_id'                  => $invoice->invoice_id,
            'merchant_response_date'      => date('Y-m-d'),
            'merchant_response_driver'    => $this->Mdl_settings->setting('merchant_driver'),
            'merchant_response'           => $merchantResponse,
            'merchant_response_reference' => ($response->reference()) ? $response->reference() : ''
        ]);

        return $merchantResponse;
    }

    /**
     * Payment cancel
     * @param $urlKey
     */
    public function payment_cancel($urlKey)
    {
        $this->payment_validate($urlKey);
        $this->session->set_flashdata('flash_message', lang('merchant_payment_cancel'));
        redirect('guest/view/invoice/' . $urlKey);
    }
}
