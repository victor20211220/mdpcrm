<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_versions extends Response_Model
{
    public $table = 'ip_versions';
    public $primary_key = 'ip_versions.version_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', FALSE);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_versions.version_date_applied DESC, ip_versions.version_file DESC');
    }

}
