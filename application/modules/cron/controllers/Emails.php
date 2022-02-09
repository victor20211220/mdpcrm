<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emails extends Base_Controller
{
    /**
     * Cron constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'email/Mdl_email'
        ]);
    }

    /**
     * Run cron task
     * @param $key
     */
    public function index($key)
    {
        if ($key != $this->config->item('cron_key')) {
            die("Not allowed: {$key}");
        }

        $this->Mdl_email->syncEmailAccounts();
    }
}
