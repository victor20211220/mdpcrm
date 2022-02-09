<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Companies extends Admin_Controller
{
    /**
     * Companies constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_companies',
            'Mdl_settings'
        ]);
    }

    /**
     * Index action
     * @param int $page
     */
    public function index($page = 0)
    {
        if ($this->userType != 0) {
            redirect('dashboard');
        }

        $this->Mdl_companies->where('company_id !=', 0);
        $this->Mdl_companies->paginate(site_url('companies/index'), $page);
        $companies = $this->Mdl_companies->result();
        $country = get_country_list(lang('cldr'));
        $country[''] = '';

        $this->layout->set([
            'companies' => $companies,
            'countries' => $country
        ]);

        $this->layout->buffer('content', 'companies/index');
        $this->layout->render();
    }

    /**
     * Form
     * @param null $id
     */
    public function form($id = null)
    {
        include APPPATH . '/libraries/iban/php-iban.php';

        if ($this->userType != 0 && $this->userType != 3) {
            redirect('dashboard');
        }
        if ($this->userType == 3 && $id != $this->companyId) {
            redirect('dashboard');
        }
        if ($this->input->post('btn_cancel', true)) {
            redirect('companies');
        }

        if ($this->Mdl_companies->run_validation(($id) ? 'validation_rules_existing' : 'validation_rules')) {
            $id = $this->Mdl_companies->save($id);
            if ($this->userType == 0) {
                $this->Mdl_settings->replicate_company_settings(0, $id);
            }

            redirect('companies/form/' . $id);
        }

        if ($id and !$this->input->post('btn_submit', true)) {
            if (!$this->Mdl_companies->prep_form($id)) {
                show_404();
            }
        }

        $data = $this->Mdl_companies->get_array_by_id($id);
        foreach ($data as $k => $v) {
            $this->Mdl_companies->set_form_value($k, $v);
        }

        $this->layout->set([
            'id'               => $id,
            'countries'        => get_country_list(lang('cldr')),
            'selected_country' => $this->Mdl_companies->form_value('company_country') ?: $this->Mdl_settings->setting('default_country')
        ]);

        $this->layout->buffer('content', 'companies/form');
        $this->layout->render();
    }

    /**
     * Singup action
     */
    public function signup()
    {
        show_404();
    }

    /**
     * Delete action
     * @param $id
     */
    public function delete($id)
    {
        if ($id != 0) {
            $this->Mdl_companies->delete($id);
        }
        redirect('companies');
    }
}
