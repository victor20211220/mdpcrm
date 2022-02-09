<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_users_access_resources extends Response_Model
{
    public $table = 'ip_users_access_resources';
    public $primary_key = 'ip_users_access_resources.ip_users_access_resource_id';

    public function user_types()
    {
        return [
            '2' => lang('guest_read_only'),
            '1' => lang('administrator'),
            '3' => lang('company_admin'),
            '0' => lang('master_admin'),
        ];
    }

    /**
     * Save resources
     * @param $params
     */
    public function save_resources($params)
    {
        $this->db->query("
            INSERT INTO {$this->table} (access_resource_id, user_id)
            VALUES(?, ?)",
            $params
        );
    }

    /**
     * Get resources for user
     * @param $params
     * @return mixed
     */
    public function get_resources_for_user($params)
    {
        $query = $this->db->query("
            SELECT *
            FROM ip_users_access_resources
                INNER JOIN ip_access_resources
                    ON ip_users_access_resources.access_resource_id = ip_access_resources.access_resource_id 
            WHERE user_id = ?
            ORDER BY ip_users_access_resources.access_resource_id",
            $params
        );

        return $query->result_array();
    }

    /**
     * Delete access resources
     * @param $params
     */
    public function delete_access_resources_by_user_id($params)
    {
        $query = $this->db->query("DELETE FROM ip_users_access_resources WHERE user_id = ?", $params);
    }
}
