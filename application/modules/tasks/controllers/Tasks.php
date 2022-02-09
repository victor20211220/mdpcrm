<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tasks extends Admin_Controller
{
    /**
     * Tasks constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('tasks');
        $this->load->module('layout');

        $this->load->model([
            'users/Mdl_users',
            'Mdl_tasks',
            'Mdl_tasks_alert',
            'Mdl_tasks_asgn',
            'Mdl_clients',
            'Mdl_invoice_groups',
            'Mdl_tax_rates'
        ]);
    }

    /**
     * All by default
     */
    public function index()
    {
        redirect('tasks/status/all');
    }

    /**
     * Edit task
     */
    public function edit_task_submit()
    {
        $data['task_name'] = $this->input->post('task_name', true);
        $data['task_description'] = $this->input->post('task_description', true);
        $data['client_id'] = $this->input->post('client_id', true);
        $data['task_finish_date'] = $this->input->post('task_finish_date', true);
        $data['task_status'] = $this->input->post('task_status', true);
        $data['total_time'] = $this->input->post('total_time', true);

        $this->db->where('task_id', $this->input->post('task_id', true));
        $this->db->update('ip_tasks', $data);

        echo 1;
    }

    /**
     * Edit task
     */
    public function edit_task()
    {
        $taskId = $this->input->post('id_task', true);
        $taskId = intval($taskId);

        $data['task'] = $this->db->query("SELECT * FROM ip_tasks WHERE task_id = {$taskId}")->result_array();
        $data['clients'] = $this->db->query("SELECT * FROM ip_clients WHERE company_id={$this->companyId}")->result_array();
        $data['task_statuses'] = $this->Mdl_tasks->statuses();

        $this->load->view('edit_task', $data);
    }

    /**
     * Assign
     */
    public function assign()
    {
        $this->status($status = 'all', $page = 0, 'assign');
    }

    /**
     * List by status
     * @param string $status
     * @param int $page
     * @param string $type
     */
    public function status($status = 'all', $page = 0, $type = 'all')
    {
        switch ($status) {

            case 'my_tasks' :
                $this->Mdl_tasks->my_tasks();
                $this->Mdl_tasks->order_by("ip_tasks_assignment.user_notified", "asc");
                $this->Mdl_tasks->order_by("ip_tasks_assignment.asgn_date_created", "desc");
                break;

            case 'not_assigned' :
                $this->Mdl_tasks->is_not_assigned();
                break;

            case 'not_started' :
                $this->Mdl_tasks->my_tasks();
                $this->Mdl_tasks->is_not_started();
                break;

            case 'in_progress' :
                $this->Mdl_tasks->my_tasks();
                $this->Mdl_tasks->is_in_progress();
                break;

            case 'complete' :
                $this->Mdl_tasks->my_tasks();
                $this->Mdl_tasks->is_complete();
                break;
        }

        $tasks = $this->Mdl_tasks->get()->result();

        foreach ($tasks as $task) {
            $task->user_assigned = $this->Mdl_tasks_asgn->get_by_task($task->task_id);
        }

        $this->layout->set('type', $type);
        $this->layout->set('tasks', $tasks);
        $this->layout->set('status', $status);
        $this->layout->set('task_statuses', $this->Mdl_tasks->statuses());
        $this->layout->buffer('content', 'tasks/index');
        $this->layout->render();
    }

    /**
     * Create
     */
    public function create()
    {
        $this->status($status = 'all', $page = 0, 'create');
    }

    public function form($id = null)
    {
        if ($this->input->post('btn_cancel', true)) {
            redirect('tasks');
        }

        if ($this->input->post('btn_submit', true) && $this->Mdl_tasks->form_value('user_notified') == 0) {
            $this->Mdl_tasks_asgn->update_notified($id);
            $this->Mdl_tasks_alert->refresh_task_alert($this->userId);
            $this->session->unset_userdata('flash:new:alert_success');
        }

        if ($this->input->post('btn_submit', true)) {
            if ($this->Mdl_tasks->run_validation('validation_rules_update')) {
                if ($this->input->post('task_status', true) == 0) {
                    $this->db->where('ip_tasks_assignment.task_id', $id);
                    $this->db->delete('ip_tasks_assignment');
                    $this->db->flush_cache();
                }

                $this->Mdl_tasks->save($id);

                redirect('tasks/status/all');
            }
        }

        if ($id and !$this->input->post('btn_submit', true)) {
            if (!$this->Mdl_tasks->prep_form($id)) {
                show_404();
            }
        }

        $clients = $this->Mdl_clients
            ->filter_where('ip_clients.company_id', $this->companyId)
            ->get()
            ->result();

        $this->layout->set([
            'task_statuses' => $this->Mdl_tasks->statuses(),
            'clients'       => $clients
        ]);

        $this->layout->buffer('content', 'tasks/form');
        $this->layout->render();
    }

    /**
     * Assign tasks frame
     */
    public function assign_tasks_frame()
    {
        $users = $this->Mdl_users
            ->filter_where('ip_users.company_id', $this->companyId)
            ->get()
            ->result();

        foreach ($users as $user) {
            $user->tasks_assigned = $this->Mdl_tasks_asgn->get_tasks_by_user_and_status(
                $user->user_id, ['1', '2']);
        }

        $unassigned_tasks = $this->Mdl_tasks->get_tasks_by_status(['0']);
        $data = [
            'users'            => $users,
            'unassigned_tasks' => $unassigned_tasks
        ];

        $this->layout->load_view('tasks/assign_task_frame', $data);
    }

    public function md_create_task()
    {
        $clients = $this->Mdl_clients
            ->filter_where('ip_clients.company_id', $this->companyId)
            ->get()->result();

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
            $clientData = $this->Mdl_clients
                ->filter_where('ip_clients.client_id', $this->input->post('client_id', true))
                ->filter_where('ip_clients.company_id', $this->companyId)
                ->get()->result();

            if (count($clientData) > 0) {
                $data['client_id'] = $clientData[0]->client_id;
                $data['client_name'] = $clientData[0]->client_name;
                $data['client_reg_number'] = $clientData[0]->client_reg_number;
                $data['client_address_1'] = $clientData[0]->client_address_1;
                $data['client_vat_id'] = $clientData[0]->client_vat_id;
            }
        }

        $this->layout->load_view('tasks/md_create_task', $data);
    }

    /**
     * Delete task
     * @param $id
     */
    public function delete($id)
    {
        $this->Mdl_tasks->delete($id);
        redirect('tasks/status/all');
    }
}
