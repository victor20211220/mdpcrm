<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_calendar extends Response_Model
{
    public $table = 'ip_calendar';
    public $primary_key = 'ip_calendar.id';

    public function validation_rules()
    {
        return [
            'user_id' => [
                'field' => 'user_id',
                'rules' => 'required'
            ],
            'title' => [
                'field' => 'title',
                'label' => 'Title',
                'rules' => 'required'
            ],
            'description' => [
                'field' => 'description',
                'label' => 'Description'
            ],
            'fullday' => [
                'field' => 'fullday',
                'label' => 'Fullday'
            ],
            'color' => [
                'field' => 'color',
                'label' => 'Color',
                'rules' => 'required'
            ],
            'date_start' => [
                'field' => 'date_start',
                'label' => 'Date start',
                'rules' => 'required'
            ],
            'date_end' => [
                'field' => 'date_end',
                'label' => 'Date end'
            ]
        ];
    }
}
