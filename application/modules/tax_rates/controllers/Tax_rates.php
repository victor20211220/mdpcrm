<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tax_rates extends Admin_Controller
{
    /**
     * Tax_rates constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Mdl_tax_rates');
    }

    /**
     * Index action
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->Mdl_tax_rates->filter_where('ip_tax_rates.company_id', $this->companyId);
        $this->Mdl_tax_rates->paginate(site_url('tax_rates/index'), $page);
        $tax_rates = $this->Mdl_tax_rates->result();

        $this->layout->set('tax_rates', $tax_rates);
        $this->layout->buffer('content', 'tax_rates/index');
        $this->layout->render();
    }

    /**
     * Form action
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel', true)) {
            redirect('tax_rates');
        }

        if ($this->input->post('btn_submit', true)) {
            $_POST['company_id'] = $this->companyId;
            if ($this->Mdl_tax_rates->run_validation()) {
                $this->Mdl_tax_rates->save($id);
                redirect('tax_rates');
            }
        }

        if ($id and !$this->input->post('btn_submit', true)) {
            if (!$this->Mdl_tax_rates->prep_form($id)) {
                show_404();
            }
        }

        $check = $this->Mdl_tax_rates->get_by_id($id);
        if ($id && $check->company_id != $this->companyId) {
            show_404();
        }

        $this->layout->buffer('content', 'tax_rates/form');
        $this->layout->render();
    }

    /**
     * Delete
     * @param $id
     */
    public function delete($id)
    {
        $this->Mdl_tax_rates->delete($id);
        redirect('tax_rates');
    }
}
