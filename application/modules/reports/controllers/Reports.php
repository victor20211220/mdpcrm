<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends Admin_Controller
{
    private $fromDate;
    private $toDate;
    private $datesFormatted;

    /**
     * Reports constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mdl_reports');

        $this->fromDate = $this->input->post('from_date', true);
        $this->toDate = $this->input->post('to_date', true);

        if ($this->fromDate && $this->toDate) {
            $this->datesFormatted = join('_', [
                (new DateTime($this->fromDate))->format('m-d-Y'),
                (new DateTime($this->toDate))->format('m-d-Y')
            ]);
        }
    }

    /**
     * Sales by client
     */
    public function sales_by_client()
    {
        if ($this->input->post('btn_submit', true) && $this->fromDate && $this->toDate) {
            $this->load->helper('mpdf');

            $data = [
                'results' => $this->Mdl_reports->sales_by_client($this->fromDate, $this->toDate),
                'from_date' => $this->fromDate,
                'to_date' => $this->toDate,
                'is_received' => 0
            ];

            $html = $this->load->view('reports/sales_by_client', $data, true);
            $filename = lang('filename_sales_by_client') . '_' . $this->datesFormatted;

            pdf_create_report_template($html, $filename, true);
        }

        $this->layout->buffer('content', 'reports/sales_by_client_index')->render();
    }

    /**
     * Expenses by supplier
     */
    public function expenses_by_supplier()
    {
        if ($this->input->post('btn_submit', true) && $this->fromDate && $this->toDate) {
            $this->load->helper('mpdf');

            $data = [
                'results' => $this->Mdl_reports->expenses_by_supplier($this->fromDate, $this->toDate),
                'from_date' => $this->fromDate,
                'to_date' => $this->toDate,
                'is_received' => 1
            ];

            $html = $this->load->view('reports/sales_by_client', $data, true);
            $filename = lang('expenses_by_supplier') . '_' . $this->datesFormatted;

            pdf_create_report_template($html, $filename, true);
        }

        $this->layout->buffer('content', 'reports/expenses_by_supplier_index')->render();
    }

    /**
     * Payment history
     */
    public function payment_history()
    {
        if ($this->input->post('btn_submit', true) && $this->fromDate && $this->toDate) {
            $this->load->helper('mpdf');

            $data = array();

            $data['from_date'] = $this->input->post('from_date', true);
            $data['to_date'] = $this->input->post('to_date', true);
            $data['results'] = $this->Mdl_reports->payment_history($this->fromDate, $this->toDate);

            $html = $this->load->view('reports/payment_history', $data, true);

            $date = (new DateTime())->format('Y-m-d');
            $filename = lang('payment_history') . '_' . $date;

            pdf_create_report_template($html, $filename, true);
        }

        $this->layout->buffer('content', 'reports/payment_history_index')->render();
    }

    /**
     * Invoice aging
     */
    public function invoice_aging()
    {
        if ($this->input->post('btn_submit', true)) {
            $this->load->helper('mpdf');

            $data = ['results' => $this->Mdl_reports->invoice_aging()];
            $html = $this->load->view('reports/invoice_aging', $data, true);

            $date = (new DateTime())->format('Y-m-d');
            $filename = lang('invoice_aging') . '_' . $date;

            pdf_create_report_template($html, $filename, true);
        }

        $this->layout->buffer('content', 'reports/invoice_aging_index')->render();
    }

    /**
     * Direct Report Aging From Menu
     */
    public function invoice_aging_get()
    {
        $this->load->helper('mpdf');

        $data = ['results' => $this->Mdl_reports->invoice_aging()];
        $html = $this->load->view('reports/invoice_aging', $data, true);

        $date = (new DateTime())->format('Y-m-d');
        $filename = lang('invoice_aging') . '_' . $date;

        pdf_create_report_template($html, $filename, true);
    }

    /**
     * Sales by year
     */
    public function sales_by_year()
    {
        if ($this->input->post('btn_submit', true) && $this->fromDate && $this->toDate) {
            $this->load->helper('mpdf');

            $result = $this->Mdl_reports->sales_by_year(
                $this->fromDate, $this->toDate,
                $this->input->post('minQuantity', true),
                $this->input->post('maxQuantity', true),
                $this->input->post('checkboxTax', true)
            );

            $data = [
                'results' => $result,
                'min_quantity' => $this->input->post('minQuantity', true),
                'max_quantity' => $this->input->post('maxQuantity', true),
                'from_date' => $this->fromDate,
                'to_date' => $this->toDate
            ];

            $html = $this->load->view('reports/sales_by_year', $data, true);
            $filename = lang('filename_sales_by_date') . '_' . $this->datesFormatted;

            pdf_create_report_template($html, $filename, true);
        }

        $this->layout->buffer('content', 'reports/sales_by_year_index')->render();
    }
}
