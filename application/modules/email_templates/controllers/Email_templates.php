<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_templates extends Admin_Controller
{
    /**
     * Email_templates constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_email_templates',
            'invoices/Mdl_templates',
            'Mdl_custom_fields'
        ]);
    }

    /**
     * Index action
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->Mdl_email_templates->filter_where('ip_email_templates.company_id', $this->companyId);
        $this->Mdl_email_templates->paginate(site_url('email_templates/index'), $page);
        $email_templates = $this->Mdl_email_templates->result();

        $this->layout->set('email_templates', $email_templates);
        $this->layout->buffer('content', 'email_templates/index');
        $this->layout->render();
    }

    /**
     * Form
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel', true)) {
            redirect('email_templates');
        }

        if ($this->input->post('is_update', true) == 0 && $this->input->post('email_template_title', true) != '') {
            $check = $this->db->get_where('ip_email_templates', [
                'email_template_title' => $this->input->post('email_template_title', true),
                'company_id'           => $this->companyId
            ])->result();
            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', lang('email_template_already_exists'));
                redirect('email_templates/form');
            }
        }

        if ($this->input->post('btn_submit', true)) {
            $_POST['company_id'] = $this->companyId;
            if ($this->Mdl_email_templates->run_validation()) {
                $this->Mdl_email_templates->save($id);
                redirect('email_templates');
            }
        }

        if ($id and !$this->input->post('btn_submit', true)) {
            if (!$this->Mdl_email_templates->prep_form($id)) {
                show_404();
            }
            $this->Mdl_email_templates->set_form_value('is_update', true);
        }

        $customFields = null;
        foreach (array_keys($this->Mdl_custom_fields->custom_tables()) as $table) {
            $customFields[$table] = $this->Mdl_custom_fields->by_table($table)->result();
        }

        $check = $this->Mdl_email_templates->getByPk($id);
        if ($id && $check->company_id != $this->companyId) {
            show_404();
        }

        $this->layout->set('custom_fields', $customFields);
        $this->layout->set('invoice_templates', $this->Mdl_templates->get_invoice_templates());
        $this->layout->set('quote_templates', $this->Mdl_templates->get_quote_templates());
        $this->layout->set('selected_pdf_template', $this->Mdl_email_templates->form_value('email_template_pdf_template'));
        $this->layout->buffer('content', 'email_templates/form');
        $this->layout->render();
    }

    /**
     * Delete
     * @param $id
     */
    public function delete($id)
    {
        $this->Mdl_email_templates->delete($id);
        redirect('email_templates');
    }
}
