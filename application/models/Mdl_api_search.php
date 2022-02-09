<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_api_search extends Response_Model
{
    public $table = 'ip_api_search';
    public $primary_key = 'ip_api_search.apis_id';

    /**
     * Default order by
     */
    public function default_order_by()
    {
    }

    /**
     * Default join
     */
    public function default_join()
    {
    }

    /**
     * Search by title
     * @param $title
     * @return null
     */
    public function search_by_title($title)
    {
        $query = $this->db->query("
            SELECT *
            FROM {$this->table}
            WHERE
                apis_req_title = '{$title}'
            LIMIT 1
        ");

        if ($query->num_rows() > 0) {
            $result = $query->result();
            return $result[0];
        }

        return null;
    }

    /**
     * Search by number
     * @param $number
     * @return null
     */
    public function search_by_number($number)
    {
        $query = $this->db->query("
            SELECT *
            FROM {$this->table}
            WHERE
                apis_req_number = '{$number}'
            LIMIT 1
        ");

        if ($query->num_rows() > 0) {
            $result = $query->result();
            return $result[0];
        }

        return null;
    }
}
