<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_tasks_alert extends Response_Model
{
    public $table = 'ip_tasks_alert';
    public $primary_key = 'ip_tasks_alert.task_alert_id';

    /**
     * Mdl_tasks_alert constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_tasks_asgn',
            'Mdl_tasks_alert'
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
     * Refresh task alert
     * @param null $userId
     */
    public function refresh_task_alert($userId = null)
    {
        if (is_null($userId)) {
            $userId = $this->session->userdata('user_id');
        }

        $this->Mdl_tasks_alert->filter_where('ip_tasks_alert.user_id', $userId);
        $this->Mdl_tasks_alert->filter_where('ip_tasks_alert.alert_type', '1');
        $this->Mdl_tasks_alert->filter_where('ip_tasks_alert.company_id', $this->session->userdata('company_id'));
        $alerts = $this->Mdl_tasks_alert->get()->result();

        foreach ($alerts as $alert) {
            $this->Mdl_tasks_alert->delete($alert->task_alert_id);
        }

        $this->Mdl_tasks_asgn->where('ip_tasks_assignment.user_id', $userId);
        $this->Mdl_tasks_asgn->where('ip_tasks_assignment.user_notified', 0);

        $all = $this->Mdl_tasks_asgn->get()->result();

        if (count($all) == 0) {
            return;
        }

        $this->Mdl_tasks_alert->save(null, [
            'user_id'       => $userId,
            'company_id'    => $this->session->userdata('company_id'),
            'alerts_number' => count($all),
            'alert_type'    => 1
        ]);
    }

    /**
     * Get alerts number
     * @return string
     */
    public function get_alerts_number()
    {

        $result = $this->Mdl_tasks_alert
            ->where('ip_tasks_alert.user_id', $this->session->userdata('user_id'))
            ->get();

        if ($result->num_rows() == 0) {
            return '';
        }

        return $result->row()->alerts_number;
    }

    public function stock_alert()
    {
        $data = [];
        if ($this->db->get_where('ip_products',
            ['company_id' => $this->session->userdata('company_id'), 'stock >' => 0])->result_array()) {
            $total = '';
            $alert = '';
            $quantity = [];
            $family = '';
            $current = '';
            $i = 0;

            $data = $this->db
                ->get_where('ip_products', [
                    'company_id' => $this->session->userdata('company_id'),
                    'stock >' => 0
                ])->result_array();

            foreach ($data as $row) {
                $invoiceItems = $this->db
                    ->get_where('ip_invoice_items', ['item_product_id' => $row['product_id']])
                    ->result_array();

                if ($invoiceItems) {
                    $this->db->select_sum('item_quantity');
                    $this->db->from('ip_invoice_items');
                    $this->db->where('item_product_id', $row['product_id']);
                    $query = $this->db->get();
                    $quantity = $query->row()->item_quantity;

                    $current = $row['stock'] - $quantity;
                    if ($current < $row['stock_alert']) {
                        if ($row['family_id'] != 0) {
                            $family = $this->db->get_where('ip_families', ['family_id' => $row['family_id']])->row('family_name');
                        } else {
                            $family = '-';
                        }

                        $data[] = '1';
                        $i++;
                    }
                }
            }

            if (!empty($data)) {
                $data = count($data);
            } else {
                $data = '';
            }
        } else {
            $data = '';
        }

        return $data;
    }
}
