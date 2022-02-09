<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Families extends Admin_Controller
{
    /**
     * Families constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mdl_families');
    }

    /**
     * Index action
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->Mdl_families->filter_where('ip_families.company_id', $this->companyId);
        $this->Mdl_families->paginate(site_url('families/index'), $page);
        $families = $this->Mdl_families->result();

        $this->layout->set('families', $families);
        $this->layout->buffer('content', 'families/index');
        $this->layout->render();
    }

    /**
     * Form
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel', true)) {
            redirect('families');
        }

        if ($this->input->post('btn_submit', true)) {
            $_POST['company_id'] = $this->companyId;

            if ($this->Mdl_families->run_validation()) {
                $this->Mdl_families->save($id);
                redirect('families');
            }
        }

        if ($id and !$this->input->post('btn_submit', true)) {
            if (!$this->Mdl_families->prep_form($id)) {
                show_404();
            }
            $this->Mdl_families->set_form_value('is_update', true);
        }

        $family = $this->Mdl_families->get_by_id($id);
        if ($id && $family->company_id != $this->companyId) {
            show_404();
        }

        $this->layout->buffer('content', 'families/form');
        $this->layout->render();
    }

    /**
     * Delete
     * @param $id
     */
    public function delete($id)
    {
        $this->Mdl_families->delete($id);
        redirect('families');
    }
}
