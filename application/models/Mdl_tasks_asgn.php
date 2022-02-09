<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_tasks_asgn extends Response_Model
{
    public $table = 'ip_tasks_assignment';
    public $primary_key = 'ip_tasks_assignment.id_asgn';
    public $date_created_field = 'asgn_date_created';

    /**
     * Mdl_tasks_asgn constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model(['Mdl_tasks']);
    }

    /**
     * Default selecet
     */
    public function default_select()
    {
        $this->db->select('ip_tasks_assignment.*,ip_users.*,ip_tasks.*');
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_tasks_assignment.asgn_date_created desc');
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_users', 'ip_tasks_assignment.user_id = ip_users.user_id', 'left');
        $this->db->join('ip_tasks', 'ip_tasks_assignment.task_id = ip_tasks.task_id', 'left');
    }

    /**
     * Default where
     */
    public function default_where()
    {
        $this->db->where('ip_tasks_assignment.company_id', $this->session->userdata('company_id'));
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [];
    }

    /**
     * Get tasks by user and statuses
     * @param $userId
     * @param $statuses
     * @return mixed
     */
    public function get_tasks_by_user_and_status($userId, $statuses)
    {
        $this->Mdl_tasks_asgn->filter_where_in('ip_tasks.task_status', $statuses);

        if ($userId) {
            $this->Mdl_tasks_asgn->filter_where('ip_tasks_assignment.user_id', $userId);
        }

        return $this->Mdl_tasks_asgn->get()->result();
    }

    /**
     * Count tasks by conditions
     * @param $userId
     * @param $statuses
     * @param DateTime|null $date
     * @return int
     */
    public function countUserTasks($userId, $statuses, DateTime $date = null)
    {
        $this->Mdl_tasks_asgn->filter_where_in('ip_tasks.task_status', $statuses);

        if ($userId) {
            $this->Mdl_tasks_asgn->filter_where('ip_tasks_assignment.user_id', $userId);
        }

        if ($date) {
            $this->Mdl_tasks_asgn->filter_where('ip_tasks_assignment.asgn_date_created > ', $date->format('Y-m-d H:i:s'));
        }

        $tasks = $this->Mdl_tasks_asgn->get()->result();

        return $tasks ? count($tasks) : 0;
    }

    /**
     * TODO: find and remove
     * @param $task_id
     * @return string
     */
    public function get_by_task($task_id)
    {
        $this->Mdl_tasks_asgn->filter_where('ip_tasks_assignment.task_id', $task_id);

        $res = $this->Mdl_tasks_asgn->get();
        if ($res->num_rows() > 0) {
            return $res->row()->user_name;
        } else {
            return '';
        }
    }

    /**
     * Assign user task
     * @param $userId
     * @param $taskId
     */
    public function assign_user_task($userId, $taskId)
    {
        $this->Mdl_tasks_asgn->where('ip_tasks_assignment.task_id', $taskId);
        $this->Mdl_tasks_asgn->where('ip_tasks_assignment.company_id', $this->session->userdata('company_id'));

        $res_rec = $this->Mdl_tasks_asgn->get();
        $check_if_exists = $res_rec->num_rows();
        $res = $res_rec->row();

        if ($check_if_exists == 1 && $res->user_id != $userId) {
            $this->Mdl_tasks_asgn->delete($res->id_asgn);
            $check_if_exists = 0;
        }

        if ($check_if_exists == 0) {
            $this->Mdl_tasks_asgn->save(null, [
                'company_id'        => $this->session->userdata('company_id'),
                'task_id'           => $taskId,
                'user_id'           => $userId,
                'user_notified'     => 0,
                'asgn_date_created' => date('Y-m-d H:i:s')
            ]);

            $this->Mdl_tasks->where('ip_tasks.task_id', $taskId);
            $this->Mdl_tasks->where('ip_tasks.task_status', 0);
            $this->Mdl_tasks->update('ip_tasks', ['task_status' => 1]);
        }
    }

    /**
     * Keep only these assigned tasks
     * @param $userId
     * @param $tasks
     * @param $status
     */
    public function keep_only_these_assigned_tasks($userId, $tasks, $status)
    {
        if (count($status) == 0) {
            $status = ['1'];
        }

        if (count($tasks) == 0) {
            $tasks = ["''"];
        }

        $this->db->query("
            DELETE ASG FROM ip_tasks_assignment ASG
                LEFT JOIN ip_tasks T ON ASG.task_id = T.task_id
            WHERE T.task_status IN (" . implode(',', $status) . ")
                AND ASG.task_id NOT IN (" . implode(',', $tasks) . ")
                AND ASG.user_id = $userId
        ");
    }

    public function update_notified($id)
    {
        $this->Mdl_tasks_asgn->where('ip_tasks_assignment.task_id', $id);
        $this->Mdl_tasks_asgn->update('ip_tasks_assignment', ['user_notified' => 1]);
    }
}
