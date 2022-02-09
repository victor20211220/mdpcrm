<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Received extends Admin_Controller
{
    /**
     * Received constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_invoices',
            'Mdl_invoice_groups',
            'users/Mdl_users'
        ]);
    }

    /**
     * Show list of invoices
     */
    public function index()
    {
        $this->Mdl_invoices->is_received();
        $invoices = $this->Mdl_invoices->get()->result();

        $count = count($invoices);
        if ($count > 0) {
            $this->db->select('setting_value');
            $this->db->where('setting_key', 'pdf_invoice_template');
            $this->db->where('company_id', $invoices[0]->company_id);
            $query = $this->db->get('ip_settings');

            if ($query->row()) {
                $pdfTemplate = $query->row()->setting_value;
            } else {
                $pdfTemplate = '';
            }

            for ($i=0 ; $i<$count ; $i++) {
                $invoices[$i]->pdf_invoice_template = $pdfTemplate;
            }
        }

        $receivingGroups = $this->Mdl_invoice_groups->getList($this->companyId, Mdl_invoice_groups::TYPE_RECEIVED);
        $companyUrl = $this->Mdl_users->getCompanyUrlByUserId($this->userId);

        $this->layout->set([
            'invoices'           => $invoices,
            'companyId'          => $this->companyId,
            'companyHash'        => md5($this->companyId),
            'receivingGroups'    => $receivingGroups,
            'companyUrl'         => $companyUrl,
            'filter_display'     => true,
            'filter_placeholder' => lang('filter_invoices'),
            'filter_method'      => 'filter_invoices'
        ]);

        $this->layout->buffer('content', 'invoices/received');
        $this->layout->render();
    }

    /**
     * Export received invoices
     */
    public function impexp()
    {
        if ($this->input->post('btn_export', true)) {
            $from_date = $this->input->post('from_date', true);
            $to_date = $this->input->post('to_date', true);
            if ($from_date == null) {
                $from_date = "2015-01-01";
            } else {
                $from_date = date_to_mysql($from_date);
            }

            if ($to_date == null) {
                $to_date = date("Y-m-d");
            } else {
                $to_date = date_to_mysql($to_date);
            }

            if ($this->input->post('export_type', true) == 'rec_inv') {
                $params['export_type'] = 'rec_inv';
                $this->load->helper('xml_iso_20022_invoice');

                // Determine received invoices
                $this->Mdl_invoices->is_received();
                $invoices = $this->Mdl_invoices->get()->result();

                $params['invoices'] = $invoices;
                $r = xml_iso_20022_invoice($params);

                if ($r == false) {
                    redirect('invoices/received/impexp');
                }
            }
        }

        $mysql_cols = $this->Mdl_invoices->get_cols_name_for_export();
        $this->layout->set(['mysql_cols' => $mysql_cols,]);
        $this->layout->buffer('content', 'invoices/exp_rec_inv');
        $this->layout->render();
    }
}
