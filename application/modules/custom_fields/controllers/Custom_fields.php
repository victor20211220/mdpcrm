<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom_fields extends Admin_Controller
{
    /**
     * Custom_fields constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Mdl_custom_fields');
    }

    /**
     * Index action
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->Mdl_custom_fields->filter_where('ip_custom_fields.company_id', $this->companyId);
        $this->Mdl_custom_fields->paginate(site_url('custom_fields/index'), $page);
        $custom_fields = $this->Mdl_custom_fields->result();

        $this->layout
            ->set('custom_fields', $custom_fields)
            ->buffer('content', 'custom_fields/index')
            ->render();
    }

    /**
     * Form action
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel', true)) {
            redirect('custom_fields');
        }

        if ($this->input->post('btn_submit', true)) {
            $_POST['company_id'] = $this->companyId;
            if ($this->Mdl_custom_fields->run_validation()) {
                $this->Mdl_custom_fields->save($id);
                redirect('custom_fields');
            }
        }

        if ($id and !$this->input->post('btn_submit', true)) {
            if (!$this->Mdl_custom_fields->prep_form($id)) {
                show_404();
            }
        }

        //restrict access from URL
        $check = $this->Mdl_custom_fields->get_by_id($id);
        if ($id && $check->company_id != $this->companyId) {
            show_404();
        }

        $this->layout
            ->set('custom_field_tables', $this->Mdl_custom_fields->custom_tables())
            ->buffer('content', 'custom_fields/form')
            ->render();
    }

    /**
     * Delete action
     * @param $id
     */
    public function delete($id)
    {
        $this->Mdl_custom_fields->delete($id);
        redirect('custom_fields');
    }
}
