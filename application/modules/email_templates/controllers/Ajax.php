<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    /**
     * Ajax constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Mdl_email_templates');
    }

    /**
     * Get content
     */
    public function get_content()
    {
        echo json_encode(
            $this->Mdl_email_templates->getByPk($this->input->post('email_template_id', true))
        );
    }
}
