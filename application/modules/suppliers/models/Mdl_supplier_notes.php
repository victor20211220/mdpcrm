<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_supplier_notes extends Response_Model

{

    public $table = 'ip_supplier_notes';

    public $primary_key = 'ip_supplier_notes.supplier_note_id';

    public function default_order_by()

    {

        $this->db->order_by('ip_supplier_notes.supplier_note_date DESC');

    }

    public function default_join()

    {

        $this->db->join('ip_users', 'ip_supplier_notes.user_id = ip_users.user_id', 'left');

    }

    public function validation_rules()

    {

        return array(

            'supplier_id' => array(

                'field' => 'supplier_id',

                'label' => lang('supplier'),

                'rules' => 'required'
            ),

            'supplier_note' => array(

                'field' => 'supplier_note',

                'label' => lang('note'),

                'rules' => 'required'
            ),

            'user_id' => array('field' => 'user_id'),
        );

    }

    public function db_array()

    {

        $db_array = parent::db_array();

        $db_array['supplier_note_date'] = date('Y-m-d');

        $db_array['user_id'] = $this->session->userdata('user_id');

        return $db_array;

    }

}
