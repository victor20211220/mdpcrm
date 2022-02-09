<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_languages extends Response_Model
{
    public $table = 'ip_languages';
    public $primary_key = 'ip_languages.language_id';

    /**
     * Get all list of languages
     * @return mixed
     */
    public function get_languages()
    {
        $query = $this->db->query("SELECT * FROM {$this->table} ORDER BY language_name ASC");
        return $query->result();
    }
}
