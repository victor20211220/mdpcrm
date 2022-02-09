<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recurring extends Admin_Controller
{
    /**
     * Recurring constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mdl_invoices_recurring');
    }

    /**
     * Index action
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->Mdl_invoices_recurring->filter_where('ip_invoices_recurring.company_id', $this->companyId);
        $this->Mdl_invoices_recurring->paginate(site_url('invoices/recurring'), $page);
        $invoices = $this->Mdl_invoices_recurring->result();

        $this->layout
            ->set('recur_frequencies', $this->Mdl_invoices_recurring->getRecurringFrequencies())
            ->set('recurring_invoices', $invoices)
            ->buffer('content', 'invoices/index_recurring')
            ->render();
    }

    /**
     * Stop invoice
     * @param $invoiceId
     */
    public function stop($invoiceId)
    {
        $this->Mdl_invoices_recurring->stop($invoiceId);
        redirect('invoices/recurring/index');
    }

    /**
     * Delete invoice
     * @param $invoiceId
     */
    public function delete($invoiceId)
    {
        $this->Mdl_invoices_recurring->delete($invoiceId);
        redirect('invoices/recurring/index');
    }
}
