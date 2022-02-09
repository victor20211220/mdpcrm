<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_item_lookups extends MY_Model
{
    public $table = 'ip_item_lookups';
    public $primary_key = 'ip_item_lookups.item_lookup_id';

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_item_lookups.item_name');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'item_name'        => [
                'field' => 'item_name',
                'label' => lang('item_name'),
                'rules' => 'required'
            ],
            'item_description' => [
                'field' => 'item_description',
                'label' => lang('description')
            ],
            'item_price'       => [
                'field' => 'item_price',
                'label' => lang('price'),
                'rules' => 'required'
            ]
        ];
    }

    /**
     * DB array
     * @return array
     */
    public function db_array()
    {
        $data = parent::db_array();
        $data['item_price'] = standardize_amount($data['item_price']);

        return $data;
    }

    /**
     * Prepare form
     * @param null $id
     * @return bool
     */
    public function prep_form($id = null)
    {
        $return = false;

        if ($id) {
            $return = parent::prep_form($id);
            $this->set_form_value('item_price', format_amount($this->form_value('item_price')));
        }

        return $return;
    }
}
