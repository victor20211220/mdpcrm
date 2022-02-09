<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_stock extends Response_Model
{
    public $table = 'ip_stock';
    public $primary_key = 'ip_stock.stock_id';
    public $date_modified_field = 'stock_update_date';

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
        $this->db->where('ip_stock.company_id', $this->session->userdata('company_id'));
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_stock.stock_update_date DESC');
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_users', 'ip_users.user_id = ip_stock.stock_user_updated');
    }

    /**
     * @param null $id
     * @return mixed
     */
    public function get_by_id($id = null)
    {
        return $this->db->get_where('ip_stock', ['stock_id' => $id])->row();
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'company_id'               => [
                'field' => 'company_id',
                'label' => lang('company_id'),
                'rules' => 'required'
            ],
            'stock_update_name'        => [
                'field' => 'stock_update_name',
                'label' => lang('stock_update_name'),
                'rules' => 'required'
            ],
            'stock_update_description' => [
                'field' => 'stock_update_description',
                'label' => lang('stock_update_description'),
                'rules' => ''
            ],
            'stock_products_updated'   => [
                'field' => 'stock_products_updated',
                'label' => lang('stock_products_updated'),
                'rules' => ''
            ],
            'stock_user_updated'       => [
                'field' => 'stock_user_updated',
                'label' => lang('stock_user_updated'),
                'rules' => ''
            ],
        ];
    }
}
