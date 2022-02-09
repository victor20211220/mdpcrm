<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller
{
    /**
     * Dashboard constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'invoices/Mdl_invoice_amounts',
            'quotes/Mdl_quote_amounts',
            'invoices/Mdl_invoices',
            'quotes/Mdl_quotes',
            'Mdl_tasks_asgn',
            'Mdl_users_access_resources'
        ]);
    }

    /**
     * Index action
     */
    public function index()
    {
        $userResources = $this->Mdl_users_access_resources->get_resources_for_user([$this->userId]);
        $resources = [];
        $displayDashboard = false;

        foreach ($userResources as $r) {
            $resources[] = $r['access_resource_id'];
        }

        if (in_array(1, $resources)) {
            $displayDashboard = true;
        }

        $quoteOverviewPeriod = $this->Mdl_settings->setting('quote_overview_period');
        $invoiceOverviewPeriod = $this->Mdl_settings->setting('invoice_overview_period');

        $recentInvoices = $this->Mdl_invoices
            ->filter_where('ip_invoices.company_id', $this->companyId)
            ->filter_where('ip_invoices.is_received', 0)
            ->limit(10)
            ->get()
            ->result();

        $recentQuotes = $this->Mdl_quotes
            ->filter_where('ip_quotes.company_id', $this->companyId)
            ->limit(10)
            ->get()
            ->result();

        $overdueInvoices = $this->Mdl_invoices
            ->filter_where('ip_invoices.company_id', $this->companyId)
            ->filter_where('ip_invoices.is_received', 0)
            ->is_overdue()
            ->limit(10)
            ->get()
            ->result();

        $invoices = $this->db
            ->get_where('ip_invoices', [
                'invoice_status_id !=' => 4,
                'company_id' => $this->companyId
            ])->result_array();

        foreach ($invoices as $i) {
            $amounts[] = $this->db->get_where('ip_invoice_amounts',
                ['invoice_id' => $i['invoice_id']])->row('invoice_balance');
        }

        if (isset($amounts)) {
            $unpaid_total = array_sum($amounts);
        } else {
            $unpaid_total = '0,00';
        }

        $invoices = $this->db->get_where('ip_invoices', [
            'invoice_status_id !=' => '4',
            'company_id'           => $this->companyId,
            'invoice_date_due <'   => date('Y-m-d')
        ])->result_array();

        foreach ($invoices as $i) {
            $amountz[] = $this->db->get_where('ip_invoice_amounts',
                ['invoice_id' => $i['invoice_id']])->row('invoice_balance');
        }

        if (isset($amountz)) {
            $overdue_total = array_sum($amountz);
        } else {
            $overdue_total = '0,00';
        }

        $taskDate = (new DateTime())->modify('-1 month');

        $this->layout
            ->set([
                'display_dashboard'       => $displayDashboard,
                'invoiceStatusTotalsThis' => $this->Mdl_invoice_amounts->getStatusTotals($this->companyId, $invoiceOverviewPeriod),
                'invoiceStatusTotalsPast' => $this->Mdl_invoice_amounts->getStatusTotals($this->companyId, 'last-month'),
                'tasks'                   => $this->Mdl_tasks_asgn->countUserTasks($this->userId, 2, null),
                'tasksNotStarted'         => $this->Mdl_tasks_asgn->countUserTasks($this->userId, 1, null),
                'tasksPast'               => $this->Mdl_tasks_asgn->countUserTasks($this->userId, 2, $taskDate),
                'tasksNotStartedPast'     => $this->Mdl_tasks_asgn->countUserTasks($this->userId, 1, $taskDate),
                'quote_status_totals'     => $this->Mdl_quote_amounts->get_status_totals($quoteOverviewPeriod),
                'invoice_status_period'   => str_replace('-', '_', $invoiceOverviewPeriod),
                'quote_status_period'     => str_replace('-', '_', $quoteOverviewPeriod),
                'invoices'                => $recentInvoices,
                'quotes'                  => $recentQuotes,
                'invoice_statuses'        => $this->Mdl_invoices->statuses(),
                'quote_statuses'          => $this->Mdl_quotes->statuses(),
                'overdue_invoices'        => $overdueInvoices,
                'unpaid_total'            => $unpaid_total,
                'overdue_total'           => $overdue_total
            ])
            ->buffer('content', 'dashboard/index')
            ->render();
    }
}
