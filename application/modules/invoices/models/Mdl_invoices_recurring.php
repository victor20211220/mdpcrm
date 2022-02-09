<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_invoices_recurring extends Response_Model
{
    public $table = 'ip_invoices_recurring';
    public $primary_key = 'ip_invoices_recurring.invoice_recurring_id';
    private $recurringFrequencies = [
        '7D' => 'calendar_week',
        '1M' => 'calendar_month',
        '1Y' => 'year',
        '3M' => 'quarter',
        '6M' => 'six_months'
    ];

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select("SQL_CALC_FOUND_ROWS ip_invoices.*,
            ip_clients.client_name,
            ip_invoices_recurring.*,
            IF(recur_end_date > date(NOW()) OR recur_end_date = '0000-00-00', 'active', 'inactive') AS recur_status",
            false
        );
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_invoices', 'ip_invoices.invoice_id = ip_invoices_recurring.invoice_id');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_invoices.client_id');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'invoice_id'       => [
                'field' => 'invoice_id',
                'rules' => 'required'
            ],
            'company_id'       => [
                'field' => 'company_id'
            ],
            'recur_start_date' => [
                'field' => 'recur_start_date',
                'label' => lang('start_date'),
                'rules' => 'required'
            ],
            'recur_end_date'   => [
                'field' => 'recur_end_date',
                'label' => lang('end_date')
            ],
            'recur_frequency'  => [
                'field' => 'recur_frequency',
                'label' => lang('every'),
                'rules' => 'required'
            ],
        ];
    }

    /**
     * DB array
     * @return array
     */
    public function db_array()
    {
        $data = parent::db_array();

        $data['recur_start_date'] = date_to_mysql($data['recur_start_date']);
        $data['recur_next_date'] = $data['recur_start_date'];

        if ($data['recur_end_date']) {
            $data['recur_end_date'] = date_to_mysql($data['recur_end_date']);
        } else {
            $data['recur_end_date'] = '0000-00-00';
        }

        return $data;
    }

    /**
     * Stop recurring invoice
     * @param $invoiceRecurringId
     */
    public function stop($invoiceRecurringId)
    {
        $this->db->where('invoice_recurring_id', $invoiceRecurringId);
        $this->db->update('ip_invoices_recurring', [
            'recur_end_date'  => date('Y-m-d'),
            'recur_next_date' => '0000-00-00'
        ]);
    }

    /**
     * @return $this
     */
    public function active()
    {
        $this->filter_where("
            recur_next_date <= date(NOW()) AND
            (
                recur_end_date > date(NOW()) OR
                recur_end_date = '0000-00-00'
            )"
        );

        return $this;
    }

    /**
     * Set next recurring date
     * @param $invoiceRecurringId
     */
    public function set_next_recur_date($invoiceRecurringId)
    {
        $invoice = $this->where('invoice_recurring_id', $invoiceRecurringId)->get()->row();
        $nextDate = increment_date($invoice->recur_next_date, $invoice->recur_frequency);

        $this->db->where('invoice_recurring_id', $invoiceRecurringId);
        $this->db->update('ip_invoices_recurring', ['recur_next_date' => $nextDate]);
    }

    /**
     * Get recurring frequencies
     * @return array
     */
    public function getRecurringFrequencies()
    {
        return $this->recurringFrequencies;
    }
}
