<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_email_templates extends Response_Model
{
    public $table = 'ip_email_templates';
    public $primary_key = 'ip_email_templates.email_template_id';

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('email_template_title');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'email_template_title'        => [
                'field' => 'email_template_title',
                'label' => lang('title'),
                'rules' => 'required'
            ],
            'company_id'                  => ['field' => 'company_id'],
            'email_template_type'         => [
                'field' => 'email_template_pdf_quote_template',
                'label' => lang('type')
            ],
            'email_template_subject'      => [
                'field' => 'email_template_subject',
                'label' => lang('subject')
            ],
            'email_template_from_name'    => [
                'field' => 'email_template_from_name',
                'label' => lang('from_name')
            ],
            'email_template_from_email'   => [
                'field' => 'email_template_from_email',
                'label' => lang('from_email')
            ],
            'email_template_cc'           => [
                'field' => 'email_template_cc',
                'label' => lang('cc')
            ],
            'email_template_bcc'          => [
                'field' => 'email_template_bcc',
                'label' => lang('bcc')
            ],
            'email_template_pdf_template' => [
                'field' => 'email_template_pdf_template',
                'label' => lang('default_pdf_template')
            ],
            'email_template_body'         => [
                'field' => 'email_template_body',
                'label' => lang('body')
            ]
        ];
    }
}
