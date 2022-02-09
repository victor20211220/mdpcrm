<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_stock_history extends Response_Model
{
    public $table = 'ip_stock_history';
    public $primary_key = 'ip_stock_history.row_id';

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    /**
     * Default where
     */
    public function default_where()
    {
        $this->db->where('ip_stock_history.company_id', $this->session->userdata('company_id'));
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_products', 'ip_stock_history.product_id = ip_products.product_id', 'left');
        $this->db->join('ip_families', 'ip_products.family_id = ip_families.family_id', 'left');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [];
    }
}
