<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_client_notes extends Response_Model
{
    public $table = 'ip_client_notes';
    public $primary_key = 'ip_client_notes.client_note_id';

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_client_notes.client_note_date DESC');
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_users', 'ip_client_notes.user_id = ip_users.user_id', 'left');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'client_id'   => [
                'field' => 'client_id',
                'label' => lang('client'),
                'rules' => 'required'
            ],
            'client_note' => [
                'field' => 'client_note',
                'label' => lang('note'),
                'rules' => 'required'
            ],
            'user_id'     => ['field' => 'user_id'],
        ];
    }

    /**
     * DB array
     * @return array
     */
    public function db_array()
    {
        $data = parent::db_array();

        $data['client_note_date'] = date('Y-m-d');
        $data['user_id'] = $this->session->userdata('user_id');

        return $data;
    }
}
