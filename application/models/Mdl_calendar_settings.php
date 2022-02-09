<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_calendar_settings extends Response_Model
{
    public $table = 'ip_calendar_settings';
    public $primary_key = 'ip_calendar_settings.id';

    public function validation_rules()
    {
        return [
            'user_id' => [
                'field' => 'user_id',
                'rules' => 'required'
            ],
            'title' => [
                'field' => 'google_id',
                'label' => 'Google calendar id'
            ],
            'defaultColor' => [
                'field' => ''
            ]
        ];
    }
}
