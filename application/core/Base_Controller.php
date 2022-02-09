<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Base_Controller extends MX_Controller
{
    public $ajax_controller = false;

    /**
     * Base_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->config->load('invoice_plane');

        if ($this->ajax_controller and !$this->input->is_ajax_request()) {
            exit;
        }

        $this->load->database();

        if (empty($this->db->hostname)) {
            redirect('/welcome');
        }

        $this->Mdl_settings->load_settings();
        $language = rtrim($this->Mdl_settings->setting('default_language'), '/');
        $this->lang->load('ip', $language);
        $this->lang->load('form_validation', $language);
        $this->lang->load('custom', $language);

        $this->load->module('layout');
    }
}
