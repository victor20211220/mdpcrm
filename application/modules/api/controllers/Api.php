<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{
    /**
     * Api constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'Mdl_api',
            'Mdl_email'
        ]);
    }

    /**
     * Sync email
     */
    public function sync_email()
    {
        $this->Mdl_api->sync_email();
    }

    /**
     * Tracker
     * @param $type
     * @param $id
     */
    public function tracker($type, $id)
    {
        $this->Mdl_email->tracker($type, $id);
    }

    /**
     * Checker
     */
    public function checker()
    {
        $this->Mdl_email->checker();
    }
}
