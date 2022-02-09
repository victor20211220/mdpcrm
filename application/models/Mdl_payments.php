<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_payments extends Response_Model
{
    public $table = 'ip_payments';
    public $primary_key = 'ip_payments.payment_id';
    public $validation_rules = 'validation_rules';

    /**
     * Mdl_payments constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('invoices/Mdl_invoice_amounts');
    }

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select("
            SQL_CALC_FOUND_ROWS ip_payment_custom.*,
            ip_payment_methods.*,
            ip_invoice_amounts.*,
            ip_clients.client_name,
            ip_clients.client_id,
            ip_invoices.invoice_number,
            ip_invoices.invoice_date_created,
            ip_payments.*
        ", false);
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_payments.payment_date DESC');
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_invoices', 'ip_invoices.invoice_id = ip_payments.invoice_id');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_invoices.client_id');
        $this->db->join('ip_invoice_amounts', 'ip_invoice_amounts.invoice_id = ip_invoices.invoice_id');
        $this->db->join('ip_payment_methods', 'ip_payment_methods.payment_method_id = ip_payments.payment_method_id', 'left');
        $this->db->join('ip_payment_custom', 'ip_payment_custom.payment_id = ip_payments.payment_id', 'left');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'invoice_id'        => [
                'field' => 'invoice_id',
                'label' => lang('invoice'),
                'rules' => 'required'
            ],
            'company_id'        => [
                'field' => 'company_id',
                'label' => lang('invoice'),
                'rules' => 'required'
            ],
            'payment_date'      => [
                'field' => 'payment_date',
                'label' => lang('date'),
                'rules' => 'required'
            ],
            'payment_amount'    => [
                'field' => 'payment_amount',
                'label' => lang('payment_amount'),
                'rules' => 'required|callback_Mdl_payments.validate_payment_amount'
            ],
            'payment_method_id' => [
                'field' => 'payment_method_id',
                'label' => lang('payment_method'),
                'rules' => 'required'
            ],
            'payment_note'      => [
                'field' => 'payment_note',
                'label' => lang('note')
            ],
            'payment_is_sepa'   => [
                'field' => 'payment_is_sepa',
                'label' => lang('note')
            ],
            'payment_is_iso'    => [
                'field' => 'payment_is_iso',
                'label' => lang('note')
            ]
        ];
    }

    /**
     * Get payments
     * @param null $clientId
     * @return mixed
     */
    public function getPayments($clientId = null)
    {
        $this->db->select("
            SQL_CALC_FOUND_ROWS ip_payment_custom.*,
            ip_payment_methods.*,
            ip_invoice_amounts.*,
            ip_clients.client_name,
            ip_clients.client_id,
            ip_invoices.invoice_number,
            ip_invoices.invoice_date_created,
            ip_payments.*
        ", false);

        $this->db->join('ip_invoices', 'ip_invoices.invoice_id = ip_payments.invoice_id');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_invoices.client_id');
        $this->db->join('ip_invoice_amounts', 'ip_invoice_amounts.invoice_id = ip_invoices.invoice_id');
        $this->db->join('ip_payment_methods', 'ip_payment_methods.payment_method_id = ip_payments.payment_method_id', 'left');
        $this->db->join('ip_payment_custom', 'ip_payment_custom.payment_id = ip_payments.payment_id', 'left');

        $this->db->order_by('ip_payments.payment_date DESC');

        if ($clientId) {
            $this->db->where('ip_invoices.client_id = ', $clientId);
        }

        return $this->get(false, false, true)->result();
    }

    /**
     * Validate payment amount
     * @param $amount
     * @return bool
     */
    public function validate_payment_amount($amount)
    {
        var_dump($amount);

        $invoiceId = $this->input->post('invoice_id', true);
        $paymentId = $this->input->post('payment_id', true);

        $invoiceBalance = $this->db
            ->where('invoice_id', $invoiceId)
            ->get('ip_invoice_amounts')
            ->row()->invoice_balance;

        if ($paymentId) {
            $payment = $this->db->where('payment_id', $paymentId)->get('ip_payments')->row();
            $invoiceBalance = $invoiceBalance + $payment->payment_amount;
        }

//        if ($amount > $invoiceBalance) {
//            return false;
//        }

        return true;
    }

    /**
     * Save data
     * @param null $id
     * @param null $data
     * @return null
     */
    public function save($id = null, $data = null)
    {
        $data = ($data) ? $data : $this->db_array();
//        echo "<pre>"; print_r($data); exit;
        $id = parent::save($id, $data);
        $this->Mdl_invoice_amounts->calculate($data['invoice_id']);

        return $id;
    }

    /**
     * DB array
     * @return array
     */
    public function db_array()
    {
        $data = parent::db_array();
        $data['payment_date'] = date_to_mysql($data['payment_date']);
        $data['payment_amount'] = $data['payment_amount'];

        return $data;
    }

    /**
     * Add payment
     * @param $invoiceId
     * @param $paymentAmount
     * @param $paymentDate
     * @param $companyId
     * @return null
     */
    public function addPayment($invoiceId, $paymentAmount, $paymentDate, $companyId)
    {
        $paymentId = parent::save(null, [
            'payment_date'   => $paymentDate,
            'payment_amount' => format_currency($paymentAmount, false),
            'invoice_id'     => $invoiceId,
            'company_id'     => $companyId
        ]);
        $this->Mdl_invoice_amounts->calculate($invoiceId);

        return $paymentId;
    }

    /**
     * Delete payment
     * @param null $id
     * @param bool $setFlash
     */
    public function delete($id = null, $setFlash = true)
    {
        $invoiceData = $this->getByPk($id);
        if ($invoiceData) {
            $invoiceId = $invoiceData->invoice_id;
            parent::delete($id);

            $this->Mdl_invoice_amounts->calculate($invoiceId);

            $this->db->select('invoice_status_id');
            $this->db->where('invoice_id', $invoiceId);
            $invoice = $this->db->get('ip_invoices')->row();

            if ($invoice->invoice_status_id == 4) {
                $this->db->where('invoice_id', $invoiceId);
                $this->db->set('invoice_status_id', 2);
                $this->db->update('ip_invoices');
            }

            delete_orphans();
        }
    }

    public function prep_form($id = null)
    {
        if (!parent::prep_form($id)) {
            return false;
        }

        if (!$id) {
            parent::set_form_value('payment_date', date('Y-m-d'));
        }

        return true;
    }

    public function by_client($client_id)
    {
        $this->filter_where('ip_clients.client_id', $client_id);

        return $this;
    }

    public function by_supplier($supplier_id)
    {
        $this->filter_where('ip_suppliers.supplier_id', $supplier_id);

        return $this;
    }

    public function get_invoice_field($field, $id)
    {
        $total = '[NA]';
        $cena = $this->db->query('SELECT invoice_id,' . $field . ' FROM ip_invoices WHERE invoice_id="' . $id . '"')->result_array();

        foreach ($cena as $c) {
            $total = $c[$field];
        }

        return $total;
    }

    public function get_payments_field($field, $id)
    {
        $total = '[NA]';
        $cena = $this->db->query('SELECT invoice_id,' . $field . ' FROM ip_payments WHERE invoice_id="' . $id . '"')->result_array();

        foreach ($cena as $c) {
            $total = $c[$field];
        }

        return $total;
    }

    public function get_client_field($field, $id)
    {
        $total = '[NA]';
        $cena = $this->db->query('SELECT client_id,' . $field . ' FROM ip_clients WHERE client_id="' . $id . '"')->result_array();

        foreach ($cena as $c) {
            $total = $c[$field];
        }

        return $total;
    }

    public function get_payment_field($field, $id)
    {
        $total = '[NA]';
        $cena = $this->db->query('SELECT payment_method_id,' . $field . ' FROM ip_payment_methods WHERE payment_method_id="' . $id . '"')->result_array();

        foreach ($cena as $c) {
            $total = $c[$field];
        }

        return $total;
    }
}
