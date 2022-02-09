<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_groups extends Admin_Controller
{
    /**
     * Invoice_groups constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mdl_invoice_groups');
    }

    /**
     * Index controller
     * @param int $page
     */
    public function index($page = 0)
    {
        $groups = [
            'default' => $this->Mdl_invoice_groups->getList($this->companyId, Mdl_invoice_groups::TYPE_DEFAULT),
            'received' => $this->Mdl_invoice_groups->getList($this->companyId, Mdl_invoice_groups::TYPE_RECEIVED)
        ];

        $this->layout
            ->set('groups', $groups)
            ->buffer('content', 'invoice_groups/index')
            ->render();
    }

    /**
     * Add / edit
     * @param $type
     * @param null $id
     */
    public function form($type, $id = null)
    {
        if (in_array($type, $this->Mdl_invoice_groups->getTypes()) == false) {
            show_404();
        }

        if ($this->input->post('btn_cancel', true)) {
            redirect('invoice_groups');
        }

        if ($this->input->post('btn_submit', true)) {
            $_POST['company_id'] = $this->companyId;
            $_POST['invoice_group_type'] = $type;
            if ($this->Mdl_invoice_groups->run_validation()) {
                $this->Mdl_invoice_groups->save($id);
                redirect('invoice_groups');
            }
        }

        if ($id and !$this->input->post('btn_submit', true)) {
            if (!$this->Mdl_invoice_groups->prep_form($id)) {
                show_404();
            }
        } elseif (!$id) {
            $this->Mdl_invoice_groups->set_form_value('invoice_group_left_pad', 0);
            $this->Mdl_invoice_groups->set_form_value('invoice_group_next_id', 1);
        }

        $check = $this->Mdl_invoice_groups->get_by_id($id);
        if ($id && $check->company_id != $this->companyId) {
            show_404();
        }

        $this->layout
            ->buffer('content', 'invoice_groups/form')
            ->render();
    }

    /**
     * Delete
     * @param $id
     */
    public function delete($id)
    {
        $data = $this->Mdl_invoice_groups->getByPk($id);
        if ($data && $data->company_id == $this->companyId) {
            $this->Mdl_invoice_groups->delete($id);
        }

        redirect('invoice_groups');
    }
}
