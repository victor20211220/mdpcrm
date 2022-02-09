<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of paysera
 *
 * @author user1
 */
class Paysera extends Base_Controller
{
    //put your code here
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('webtopay/webtopay');
        $this->load->model('paysera/Mdl_payment_codes');
    }

    public function pay()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->load->model('invoices/Mdl_invoices');

        //check if the code in the url is correct
        //https://my.mdpcrm.com/paysera/pay?p=^GYGT^&6554gds&q=20
        if (isset($_GET['p']) && !empty($_GET['p']) && isset($_GET['q']) && !empty($_GET['q']))
        {
            $invoice_id = $_GET['q'];
            $invoice = $this->Mdl_invoices->get_invoice_by_id($invoice_id);
            $code_row = $this->Mdl_payment_codes->get_code_by_column('code', $_GET['p']);

            $buyer_email = '';
            if (isset($_GET['e']) && !empty($_GET['e']))
                $buyer_email = $_GET['e'];

            if (isset($code_row) && !empty($code_row))
            {

                //verify if the unique code is in the database
                $this->load->model('Mdl_payment_codes');

                //invalidate unique code in callback after the payment is complete

                $accept_url = base_url() . '/paysera/payment_complete';
                $cancel_url = base_url();
                $callback_url = base_url() . '/paysera/callback';

                try
                {
                    $project_id = $this->Mdl_settings->setting('paysera_project_id');
                    //93171; //0
                    $test = $this->Mdl_settings->setting('merchant_driver_mode');
                    //1 = testing
                    ///$sign_password = 'd41d8cd98f00b204e9800998ecf8427e';
                    $sign_password = $this->Mdl_settings->setting('paysera_sign_password');
                    //'2a8a812400df8963b2e2ac0ed01b07b8';
                    //var_dump($sign_password); exit;
                    $payment_currency = $this->Mdl_settings->setting('merchant_currency_code');
                    if (isset($payment_currency))
                    {
                        $payment_currency = $this->Mdl_settings->setting('merchant_currency_code');
                    }
                    else
                    {
                        $payment_currency = "EUR";
                    }

                    $amount = $invoice[0]['invoice_total'] * 100;

                    $requestUrl = WebToPay::redirectToPayment(array(
                        'projectid' => $project_id,
                        'sign_password' => $sign_password,
                        'orderid' => $invoice_id,
                        'amount' => $amount,
                        'currency' => $payment_currency,
                        'p_email' => $buyer_email,
                        //'country'       => 'LT',
                        'accepturl' => $accept_url,
                        'cancelurl' => $cancel_url,
                        'callbackurl' => $callback_url,
                        'test' => $test,
                    ));

                    //                echo '<a href="' . $requestUrl . '" >Pay</a>';
                }
                catch (WebToPayException $e)
                {
                    // handle exception
                }
            }
            else
            {
                echo "Wrong URL.";
            }
        }
        else
        {
            echo "Wrong URL.";
        }

        //if the code is correct redirect the user to the paysera page
    }

    public function callback()
    {
        echo '<meta name="verify-paysera" content="7e9a396ba2e130be62a21f6e515a7a06">';
        //        $order_id = 123;
        //        $amount = 12.09;
        //        $date_used = date("Y-m-d H:i:s");
        //        $this->load->model('payments/Mdl_payments');
        //        $this->load->model('invoices/Mdl_invoices');
        //        $invoice = $this->Mdl_invoices->get_invoice_by_id($order_id);
        //
        //        $company_id = $invoice[0]['company_id'];
        //        $this->Mdl_payments->add_payment($order_id, $amount, $date_used, $company_id);
        //        exit;

        try
        {
            $project_id = $this->Mdl_settings->setting('paysera_project_id');
            //93171; //0
            $sign_password = $this->Mdl_settings->setting('paysera_sign_password');
            //'2a8a812400df8963b2e2ac0ed01b07b8';

            $response = WebToPay::checkResponse($_GET, array(
                'projectid' => $project_id,
                'sign_password' => $sign_password,
            ));

            //            if ($response['test'] !== '0') {
            //                throw new Exception('Testing, real payment was not made');
            //            }
            //            if ($response['type'] !== 'macro') {
            //                throw new Exception('Only macro payment callbacks are accepted');
            //            }

            $myfile = fopen(getcwd() . "/newfile.txt", "w") or die("Unable to open file!");
            $txt = print_r($response, true);
            fwrite($myfile, $txt);
            fclose($myfile);

            $order_id = $response['orderid'];
            $amount = $response['amount'] / 100;
            //add this into paysera payments table
            //            $currency = $response['currency'];

            //invalidate code on database

            $code_row = $this->Mdl_payment_codes->get_code_by_column('invoice_id', $order_id);
            //            var_dump($order_id);
            //             var_dump($code_row);
            //            exit;
            if (isset($code_row) && !empty($code_row) && $code_row[0]['used'] == 0)
            {
                //== 0
                $date_used = date("Y-m-d H:i:s");
                $this->Mdl_payment_codes->update_code_by_invoice_id($order_id, $date_used);

                $this->load->model('Mdl_payments');
                $this->load->model('invoices/Mdl_invoices');

                $invoice = $this->Mdl_invoices->get_invoice_by_id($order_id);
                //                var_dump($invoice);
                //                exit;

                $company_id = $invoice[0]['company_id'];
                $this->Mdl_payments->addPayment($order_id, $amount, $date_used, $company_id);
            }

            //@todo: patikrinti, ar užsakymas su $orderId dar nepatvirtintas (callback gali būti pakartotas kelis kartus)
            //@todo: patikrinti, ar užsakymo suma ir valiuta atitinka $amount ir $currency
            //@todo: patvirtinti užsakymą

            echo 'OK';
        }
        catch (Exception $e)
        {
            $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
            $txt = get_class($e) . ': ' . $e->getMessage();
            fwrite($myfile, $txt);
            fclose($myfile);
        }
    }

    public function payment_complete()
    {
        $this->callback();
        redirect('paysera/thank_you');
    }

    public function thank_you()
    {
        $this->load->view('payment_complete');
    }

}
