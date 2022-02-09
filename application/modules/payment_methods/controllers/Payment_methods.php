<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_methods extends Admin_Controller
{
    /**
     * Payment_methods constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Mdl_payment_methods');
    }

    /**
     * Index action
     * @param int $page
     */
    public function index($page = 0)
    {
        $data = $this->Mdl_payment_methods
            ->filter_where('ip_payment_methods.company_id', $this->companyId)
            ->paginate(site_url('payment_methods/index'), $page)
            ->result();

        $this->layout
            ->set('payment_methods', $data)
            ->buffer('content', 'payment_methods/index')
            ->render();
    }

    /**
     * Form action
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel', true)) {
            redirect('payment_methods');
        }

        if ($this->input->post('is_update', true) == 0 && $this->input->post('payment_method_name', true) != '') {

            $check = $this->db
                ->get_where('ip_payment_methods', [
                    'payment_method_name' => $this->input->post('payment_method_name', true),
                    'company_id'          => $this->companyId
                ])->result();

            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', lang('payment_method_already_exists'));
                redirect('payment_methods/form');
            }
        }

        if ($this->input->post('btn_submit', true)) {
            $_POST['company_id'] = $this->companyId;
            if ($this->Mdl_payment_methods->run_validation()) {
                $this->Mdl_payment_methods->save($id);
                redirect('payment_methods');
            }
        }

        if ($id and !$this->input->post('btn_submit', true)) {
            if (!$this->Mdl_payment_methods->prep_form($id)) {
                show_404();
            }
            $this->Mdl_payment_methods->set_form_value('is_update', true);
        }

        //restrict access from URL
        $check = $this->Mdl_payment_methods->getByPk($id);
        if ($id && $check->company_id != $this->companyId) {
            show_404();
        }

        $this->layout
            ->buffer('content', 'payment_methods/form')
            ->render();
    }

    /**
     * Delete action
     * @param $id
     */
    public function delete($id)
    {
        $this->Mdl_payment_methods->delete($id);
        redirect('payment_methods');
    }
}
