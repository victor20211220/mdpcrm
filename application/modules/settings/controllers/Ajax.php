<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function get_cron_key()
    {
        echo random_string('alnum', 16);
    }
}
