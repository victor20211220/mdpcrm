<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    /**
     * Ajax constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->module('layout');
        $this->load->model([
            'Mdl_invoice_groups',
            'Mdl_tax_rates',
            'Mdl_tasks',
            'Mdl_tasks_asgn',
            'Mdl_tasks_alert',
            'Mdl_clients',
            'users/Mdl_users'
        ]);
    }

    /**
     * Create
     */
    public function create()
    {
        $_POST['company_id'] = $this->companyId;
        $_POST['task_date_created'] = date('Y-m-d H:i:s');

        if ($this->Mdl_tasks->run_validation()) {
            $taskId = $this->Mdl_tasks->save();
            $response = [
                'success' => 1,
                'task_id' => $taskId
            ];

            $this->db->insert('ip_tasks_time', [
                'task_id'    => $taskId,
                'status'     => '0',
                'date'       => date('Y-m-d H:i:s'),
                'total_time' => '0'
            ]);
        } else {
            $response = [
                'success'           => 0,
                'validation_errors' => json_errors()
            ];
        }

        echo json_encode($response);
    }

    /**
     * Modal create task
     */
    public function modal_create_task()
    {
        $clients = $this->Mdl_clients
            ->filter_where('ip_clients.company_id', $this->companyId)
            ->get()
            ->result();

        $taskStatuses = $this->Mdl_tasks->statuses();

        $data = [
            'clients'           => $clients,
            'task_statuses'     => $taskStatuses,
            'client_id'         => '-1',
            'client_name'       => '',
            'client_reg_number' => '',
            'client_address_1'  => '',
            'client_vat_id'     => ''
        ];

        if ($this->input->post('client_id', true)) {
            $client_data = $this->Mdl_clients
                ->filter_where('ip_clients.client_id', $this->input->post('client_id', true))
                ->filter_where('ip_clients.company_id', $this->companyId)
                ->get()->result();

            if (count($client_data) > 0) {
                $data['client_id'] = $client_data[0]->client_id;
                $data['client_name'] = $client_data[0]->client_name;
                $data['client_reg_number'] = $client_data[0]->client_reg_number;
                $data['client_address_1'] = $client_data[0]->client_address_1;
                $data['client_vat_id'] = $client_data[0]->client_vat_id;
            }
        }

        $this->layout->load_view('tasks/modal_create_task', $data);
    }

    public function modal_assign_tasks()
    {
        $users = $this->Mdl_users
            ->filter_where('ip_users.company_id', $this->companyId)
            ->get()
            ->result();

        $orphan_tasks = $this->Mdl_tasks->is_not_assigned()->get()->result();
        $task_statuses = $this->Mdl_tasks->statuses();

        $data = [
            'task_statuses'     => $task_statuses,
            'orphan_tasks'      => $orphan_tasks,
            'users'             => $users,
            'clients'           => [],
            'client_id'         => '-1',
            'client_name'       => '',
            'client_reg_number' => '',
            'client_address_1'  => '',
            'client_vat_id'     => ''
        ];

        $this->layout->load_view('tasks/modal_assign_task', $data);
    }

    function update_order()
    {
        $user_id = $this->input->post('status', true);
        $tasks = $this->input->post('project_id', true);
        $status = ['0', '1'];

        if (!is_array($tasks)) {
            $tasks = [];
        }

        if ($user_id == '000') {
            foreach ($tasks as $task_id) {
                $this->db->where('task_id', $task_id);
                $this->db->delete('ip_tasks_assignment');
                $this->db->flush_cache();
                $this->db->where('task_id', $task_id);
                $this->db->update('ip_tasks', ['task_status' => 0]);
                $this->db->flush_cache();
            }

            $this->session->unset_userdata('flash:new:alert_success');

            echo 'TRUE';
            exit;
        }

        $this->Mdl_tasks_asgn->keep_only_these_assigned_tasks($user_id, $tasks, $status);

        foreach ($tasks as $task_id) {
            $this->Mdl_tasks_asgn->assign_user_task($user_id, $task_id);
        }

        $this->Mdl_tasks_alert->refresh_task_alert($user_id);
        $this->session->unset_userdata('flash:new:alert_success');

        echo 'TRUE';
    }
}
