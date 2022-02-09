<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends Admin_Controller
{
    /**
     * Projects constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_projects',
            'Mdl_clients'
        ]);
    }

    /**
     * Index action
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->Mdl_projects->paginate(site_url('projects/index'), $page);
        $projects = $this->Mdl_projects->result();

        $this->layout
            ->set('projects', $projects)
            ->buffer('content', 'projects/index')
            ->render();
    }

    /**
     * Form
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel', true)) {
            redirect('projects');
        }

        if ($this->Mdl_projects->run_validation()) {
            $this->Mdl_projects->save($id);
            redirect('projects');
        }

        if ($id and !$this->input->post('btn_submit', true)) {
            if (!$this->Mdl_projects->prep_form($id)) {
                show_404();
            }
        }

        $this->layout
            ->set(['clients' => $this->Mdl_clients->where('client_active', 1)->get()->result()])
            ->buffer('content', 'projects/form')
            ->render();
    }

    /**
     * Delete
     * @param $id
     */
    public function delete($id)
    {
        $this->Mdl_projects->delete($id);
        redirect('projects');
    }
}
