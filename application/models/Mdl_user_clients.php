<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_user_clients extends MY_Model
{
    public $table = 'ip_user_clients';
    public $primary_key = 'ip_user_clients.user_client_id';

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('ip_user_clients.*, ip_users.user_name, ip_clients.client_name');
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_users', 'ip_users.user_id = ip_user_clients.user_id');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_user_clients.client_id');
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_clients.client_name');
    }

    /**
     * Assigned to
     * @param $userId
     * @return $this
     */
    public function assigned_to($userId)
    {
        $this->filter_where('ip_user_clients.user_id', $userId);

        return $this;
    }
}
