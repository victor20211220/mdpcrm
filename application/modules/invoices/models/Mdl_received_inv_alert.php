<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_received_inv_alert extends Response_Model
{
    public $table = 'ip_received_inv_alert';
    public $primary_key = 'ip_received_inv_alert.rec_alert_id';

    /**
     * Mdl_received_inv_alert constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_clients',
            'Mdl_companies',
            'invoices/Mdl_invoices',
        ]);
    }

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    /**
     * Check alert
     * @param $clientId
     */
    public function check_alert($clientId)
    {
        $client = $this->Mdl_clients->get_by_id($clientId);
        $regNumber = $client->client_reg_number;

        $me_as_client = $this->Mdl_clients->where('client_reg_number', $regNumber)->get()->result();
        $clients_array = [];
        $clients_array[] = -1;

        foreach ($me_as_client as $client) {
            $clients_array[] = $client->client_id;
        }

        $big_company = $this->Mdl_companies->where('company_code', $regNumber)->get()->row();
        $all_invoices = $this->Mdl_invoices->where('
            (
                ip_invoices.client_id IN (' . implode(",", $clients_array) . ') AND
                ip_invoices.is_read_only=1 AND
                ip_invoices.invoice_status_id=2 AND
                ip_invoices.is_received=0 AND
                ip_invoices.is_rec_and_seen=0
            ) ', null, false
        )->get()->result();

        if (isset($big_company->company_id)) {
            $com_id = $big_company->company_id;
        } else {
            $com_id = 'XX';
        }

        $alert_old = $this->Mdl_received_inv_alert
            ->where('ip_received_inv_alert.company_id', $com_id)
            ->get()
            ->row();

        if (isset($alert_old->rec_alert_id)) {
            $this->Mdl_received_inv_alert->delete($alert_old->rec_alert_id);
        }

        if (count($all_invoices) > 0 && $com_id != 'XX') {
            $this->Mdl_received_inv_alert->save(null, [
                'company_id' => $com_id,
                'rec_alert_number' => count($all_invoices)
            ]);
        }
    }

    /**
     * Check alert on my comp
     */
    public function check_alert_on_my_comp()
    {
        $regNumber = $this->Mdl_companies
            ->where('company_id', $this->session->userdata('company_id'))
            ->get()
            ->row()
            ->company_code;

        $me_as_client = $this->Mdl_clients->where('client_reg_number', $regNumber)->get()->result();

        $clients_array = [];

        $clients_array[] = -1;

        foreach ($me_as_client as $client) {
            $clients_array[] = $client->client_id;
        }

        $all_invoices = $this->Mdl_invoices->where('
            (
                ip_invoices.client_id IN (' . implode(",", $clients_array) . ') AND
                ip_invoices.is_read_only=1 AND
                ip_invoices.invoice_status_id=2 AND
                ip_invoices.is_received=0 AND
                ip_invoices.is_rec_and_seen=0
            )', null, false
        )->get()->result();

        $alert_old = $this->Mdl_received_inv_alert
            ->where('ip_received_inv_alert.company_id', $this->session->userdata('company_id'))
            ->get()
            ->row();

        if (isset($alert_old->rec_alert_id)) {
            $this->Mdl_received_inv_alert->delete($alert_old->rec_alert_id);
        }

        if (count($all_invoices) > 0) {
            $this->Mdl_received_inv_alert->save(null, [
                'company_id' => $this->session->userdata('company_id'),
                'rec_alert_number' => count($all_invoices)
            ]);
        }
    }

    /**
     * Get alerts number
     * @return string
     */
    public function get_alerts_number()
    {
        $row = $this->Mdl_received_inv_alert
            ->where('ip_received_inv_alert.company_id', $this->session->userdata('company_id'))
            ->get()
            ->row();

        return isset($row->rec_alert_number) ? $row->rec_alert_number : '';
    }
}
