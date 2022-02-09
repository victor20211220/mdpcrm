<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_tasks extends Response_Model
{
    public $table = 'ip_tasks';
    public $primary_key = 'ip_tasks.task_id';

    const STATUS_NOT_ASSIGNED = 0;
    const STATUS_NOT_STARTED = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_COMPLETE = 3;

    /**
     * Mdl_tasks constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_tasks_asgn',
            'Mdl_clients'
        ]);
    }

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('
            ip_tasks.*,
            ip_tasks_assignment.*,
            ip_clients.*,
            ip_tasks.task_id as task_id,
            (CASE WHEN DATEDIFF(NOW(), task_finish_date) > 0 THEN 1 ELSE 0 END) is_overdue
        ', false);
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_projects.project_name, ip_tasks.task_name');
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_projects', 'ip_projects.project_id = ip_tasks.project_id', 'left');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_tasks.client_id', 'left');
        $this->db->join('ip_tasks_assignment', 'ip_tasks.task_id = ip_tasks_assignment.task_id', 'left');
    }

    /**
     * Default where
     */
    public function default_where()
    {
        $this->db->where('ip_tasks.company_id', $this->session->userdata('company_id'));
    }

    /**
     * By task
     * @param $match
     */
    public function by_task($match)
    {
        $this->db->like('task_name', $match);
        $this->db->or_like('task_description', $match);
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'task_name' => [
                'field' => 'task_name',
                'label' => lang('task_name'),
                'rules' => 'required'
            ],

            'task_description' => [
                'field' => 'task_description',
                'label' => lang('task_description'),
                'rules' => ''
            ],

            'task_price' => [
                'field' => 'task_price',
                'label' => lang('task_price'),
                'rules' => ''
            ],

            'task_date_created' => [
                'field' => 'task_date_created',
                'label' => lang('task_date_created')
            ],

            'task_finish_date' => [
                'field' => 'task_finish_date',
                'label' => lang('task_finish_date'),
                'rules' => 'required'
            ],

            'project_id' => [
                'field' => 'project_id',
                'label' => lang('project'),
                'rules' => ''
            ],

            'task_status' => [
                'field' => 'task_status',
                'label' => lang('status')
            ],

            'client_name' => [
                'field' => 'client_name',
                'label' => lang('client'),
                'rules' => 'required'
            ],

            'client_id' => [
                'field' => 'client_name',
                'label' => lang('client'),
                'rules' => 'required'
            ],

            'client_reg_number' => [
                'field' => 'client_reg_number',
                'label' => lang('client_reg_number'),
                'rules' => 'required'
            ],

            'client_address_1' => [
                'field' => 'client_address_1',
                'label' => lang('client_address_1'),
                'rules' => 'required'
            ],

            'client_vat_id' => [
                'field' => 'client_vat_id',
                'label' => lang('client_vat_id')
            ],

            'company_id' => ['field' => 'company_id'],
            'total_time' => ['field' => 'total_time']
        ];

    }

    /**
     * Validation rules for update
     * @return array
     */
    public function validation_rules_update()
    {
        return [
            'task_name' => [
                'field' => 'task_name',
                'label' => lang('task_name'),
                'rules' => 'required'
            ],

            'task_description' => [
                'field' => 'task_description',
                'label' => lang('task_description'),
                'rules' => ''
            ],

            'task_finish_date' => [
                'field' => 'task_finish_date',
                'label' => lang('task_finish_date'),
                'rules' => 'required'
            ],

            'task_status' => [
                'field' => 'task_status',
                'label' => lang('status'),
                'rules' => ''
            ],

            'client_id' => [
                'field' => 'client_id',
                'label' => lang('client'),
                'rules' => ''
            ],

            'total_time' => [
                'field' => 'total_time',
                'label' => lang('time'),
                'rules' => ''
            ]
        ];
    }

    public function prep_form($id = null)
    {
        if (!parent::prep_form($id)) {
            return false;
        }

        if (!$id) {
            parent::set_form_value('task_finish_date', date('Y-m-d'));
        }

        return true;
    }

    /**
     * Statuses
     * @return array
     */
    public function statuses()
    {
        return [
            self::STATUS_NOT_ASSIGNED => [
                'label' => lang('not_assigned'),
                'class' => 'orphan'
            ],

            self::STATUS_NOT_STARTED => [
                'label' => lang('not_started'),
                'class' => 'draft'
            ],

            self::STATUS_IN_PROGRESS => [
                'label' => lang('in_progress'),
                'class' => 'viewed'
            ],

            self::STATUS_COMPLETE => [
                'label' => lang('complete'),
                'class' => 'sent'
            ]
        ];
    }

    /**
     * Is not assigned
     * @return $this
     */
    public function is_not_assigned()
    {
        $this->filter_where('ip_tasks.task_status', self::STATUS_NOT_ASSIGNED);

        return $this;
    }

    /**
     * Is not started
     * @return $this
     */
    public function is_not_started()
    {
        $this->filter_where('ip_tasks.task_status', self::STATUS_NOT_STARTED);

        return $this;
    }

    /**
     * Is in progress
     * @return $this
     */
    public function is_in_progress()
    {
        $this->filter_where('ip_tasks.task_status', self::STATUS_IN_PROGRESS);

        return $this;
    }

    /**
     * Is complete
     * @return $this
     */
    public function is_complete()
    {
        $this->filter_where('ip_tasks.task_status', self::STATUS_COMPLETE);

        return $this;
    }

    /**
     * My tasks
     * @return $this
     */
    public function my_tasks()
    {
        $this->Mdl_tasks_asgn->where('ip_tasks_assignment.user_id', $this->session->userdata('user_id'));
        $tasks = $this->Mdl_tasks_asgn->get()->result();

        $ids = [-1];
        foreach ($tasks as $task) {
            $ids[] = $task->task_id;
        }

        $this->filter_where_in('ip_tasks.task_id', $ids);

        return $this;
    }

    /**
     * DB array
     * @return array
     */
    public function db_array()
    {
        $data = parent::db_array();

        if ((!isset($data['client_id'])) || (isset($data['client_id']) && $data['client_id'] == -1)) {
            $dataClient = [
                'client_name'       => $data['client_name'],
                'company_id'        => $this->session->userdata('company_id'),
                'client_reg_number' => $data['client_reg_number'],
                'client_address_1'  => $data['client_address_1'],
                'client_vat_id'     => $data['client_vat_id']
            ];

            $clientId = $this->Mdl_clients->save(null, $dataClient);
            $data['client_id'] = $clientId;
        }

        unset($data['client_reg_number']);
        unset($data['client_address_1']);
        unset($data['client_vat_id']);
        unset($data['client_vat_id']);
        unset($data['client_name']);

        if (!isset($data['task_status'])) {
            $data['task_status'] = 0;
        }

        $data['task_finish_date'] = date_to_mysql($data['task_finish_date']);

        return $data;
    }

    /**
     * Get tasks by status
     * @param array $status
     * @return mixed
     */
    public function get_tasks_by_status($status = [])
    {
        $this->Mdl_tasks->filter_where_in('ip_tasks.task_status', $status);
        $this->Mdl_tasks->filter_where('ip_tasks.company_id', $this->session->userdata('company_id'));
        $res = $this->Mdl_tasks->get()->result();

        return $res;
    }

    /**
     * Get task by clientId
     * @param $clientId
     * @return array
     */
    public function get_task_by_id($clientId)
    {
        $taskStatus = '';
        $tasks = [];

        foreach ($this->db->get_where('ip_tasks', ['client_id' => $clientId])->result_array() as $task) {
            if ($task['task_status'] == 0) {
                $taskStatus = lang('not_assigned');
            } elseif ($task['task_status'] == 1) {
                $taskStatus = lang('not_started');
            } elseif ($task['task_status'] == 2) {
                $taskStatus = lang('in_progress');
            } elseif ($task['task_status'] == 3) {
                $taskStatus = lang('complete');
            }

            $userAssigned = $this->db->get_where(
                'ip_tasks_assignment',
                ['task_id' => $task['task_id']]
            )->row('user_id');

            if ($userAssigned) {
                $user = $this->db->get_where('ip_users', ['user_id' => $userAssigned])->row('user_name');
            } else {
                $user = '-';
            }

            $total_time = $this->db->get_where('ip_tasks_time', ['task_id' => $task['task_id']])->row('total_time');

            $tasks[] = [
                'task_id'           => $task['task_id'],
                'task_name'         => $task['task_name'],
                'task_status'       => $taskStatus,
                'user_assigned'     => $user,
                'task_date_created' => strtotime($task['task_date_created']),
                'task_finish_date'  => strtotime($task['task_finish_date']),
                'total_time'        => $total_time,
            ];
        }

        return $tasks;
    }
}
