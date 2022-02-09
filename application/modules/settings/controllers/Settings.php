<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends Admin_Controller
{
    /**
     * Settings constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'invoices/Mdl_templates',
            'Mdl_companies',
            'Mdl_email_templates',
            'Mdl_invoice_groups',
            'Mdl_languages',
            'Mdl_payment_methods',
            'Mdl_settings',
            'Mdl_tax_rates',
            'Mdl_versions',
        ]);
    }

    /**
     * Index action
     */
    public function index()
    {
        if ($this->input->post('settings', true)) {
            $settings = $this->input->post('settings', true);

            $settings['local_license_key'] = '';

            if (isset($settings['pdf_invoice_template']) && !empty($settings['pdf_invoice_template'])) {
                $settings['pdf_quote_template'] = $settings['pdf_invoice_template'];
            }

            foreach ($settings as $key => $value) {
                if ($key == 'currency_symbol' and strlen($value) != 3) {
                    $this->session->set_flashdata('alert_error', lang('currency_3_letter'));
                    redirect('settings');
                }

                if ($key == 'smtp_password' or $key == 'merchant_password') {
                    if ($value <> '') {
                        $this->load->library('encrypt');
                        $this->Mdl_settings->save($key, $this->encrypt->encode($value));
                    }
                } else {
                    $this->Mdl_settings->save($key, $value);
                }
            }

            $uploadConfig = [
                'upload_path'   => './uploads/',
                'allowed_types' => 'gif|jpg|png|svg',
                'max_size'      => '999999',
                'max_width'     => '10000000',
                'max_height'    => '10000000',
                'encrypt_name'  => true
            ];

            // Check for invoice logo upload

            if ($_FILES['invoice_logo']['name']) {
                $this->load->library('upload', $uploadConfig);
                if (!$this->upload->do_upload('invoice_logo')) {
                    $this->session->set_flashdata('alert_error', $this->upload->display_errors());
                    redirect('settings');
                } else {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = './uploads/' . $this->upload->data('file_name');
                    $config['create_thumb'] = true;
                    $config['maintain_ratio'] = false;
                    $config['width'] = 250;

                    $this->load->library('image_lib', $config);

                    $this->image_lib->resize();
                    $upload_data = $this->upload->data();
                    $this->Mdl_settings->save('invoice_logo', str_replace('.', '_thumb.', $upload_data['file_name']));
                }
            }

            // Check for login logo upload

            if ($_FILES['login_logo']['name']) {
                $this->load->library('upload', $uploadConfig);
                if (!$this->upload->do_upload('login_logo')) {
                    $this->session->set_flashdata('alert_error', $this->upload->display_errors());
                    redirect('settings');
                }

                $upload_data = $this->upload->data();
                $this->Mdl_settings->save('login_logo', $upload_data['file_name']);
            }

            // test smtp settings

            $this->load->helper('mailer');
            if ($settings['email_send_method'] == 'smtp') {

                // generates an error probably because the smtp credentials are not correct. SOLVED

                if (!test_smtp()) {
                    redirect('settings');
                }
            }

            $this->session->set_flashdata('alert_success', lang('settings_successfully_saved'));
            redirect('settings');
        }


        $this->load->library('merchant');

        if ($this->companyId != null) {
            $company_row = $this->db
                ->get_where('ip_companies', ['company_id' => $this->companyId])
                ->row_array();

            if ($company_row) {
                $company = $company_row;
            } else {
                $company = null;
            }
        } else {
            $company = null;
        }

        $pdfInvoiceTemplates = $this->Mdl_templates->get_invoice_templates('pdf');
        $publicInvoiceTemplates = $this->Mdl_templates->get_invoice_templates('public');
        $publicQuoteTemplates = $this->Mdl_templates->get_quote_templates('public');

        $current_version = $this->Mdl_versions->limit(1)->where('version_sql_errors', 0)->get()->row()->version_file;
        $current_version = str_replace('.sql', '', substr($current_version, strpos($current_version, '_') + 1));

        // Set data in the layout

        $invoiceGroups = $invoiceGroups = $this->Mdl_invoice_groups
            ->filter_where('ip_invoice_groups.company_id', $this->companyId)
            ->get()
            ->result();

        $taxRates = $this->Mdl_tax_rates
            ->filter_where('ip_tax_rates.company_id', $this->companyId)
            ->get()
            ->result();

        $paymentMethods = $this->Mdl_payment_methods
            ->filter_where('ip_payment_methods.company_id', $this->companyId)
            ->get()
            ->result();

        $emailTemplatesQuote = $this->Mdl_email_templates
            ->where('email_template_type', 'quote')
            ->where('company_id', $this->companyId)
            ->get()
            ->result();

        $emailTemplatesInvoice = $this->Mdl_email_templates
            ->where('email_template_type', 'invoice')
            ->where('company_id', $this->companyId)
            ->get()
            ->result();

        $this->layout->set([
            'invoice_groups'           => $invoiceGroups,
            'tax_rates'                => $taxRates,
            'payment_methods'          => $paymentMethods,
            'public_invoice_templates' => $publicInvoiceTemplates,
            'pdf_invoice_templates'    => $pdfInvoiceTemplates,
            'public_quote_templates'   => $publicQuoteTemplates,
            'company'                  => $company,
            'languages'                => $this->Mdl_languages->get_languages(),
            'countries'                => get_country_list(lang('cldr')),
            'date_formats'             => date_formats(),
            'current_date'             => new DateTime(),
            'email_templates_quote'    => $emailTemplatesQuote,
            'email_templates_invoice'  => $emailTemplatesInvoice,
            'merchant_drivers'         => $this->merchant->valid_drivers(),
            'merchant_currency_codes'  => Merchant::$NUMERIC_CURRENCY_CODES,
            'current_version'          => $current_version,
            'first_days_of_weeks'      => [
                "1" => lang("monday"),
                "0" => lang("sunday")
            ]
        ]);

        $this->layout->buffer('content', 'settings/index');
        $this->layout->render();
    }

    /**
     * Remove logo
     * @param $type
     */
    public function remove_logo($type)
    {
        unlink('./uploads/' . $this->Mdl_settings->setting($type . '_logo'));
        $this->Mdl_settings->save($type . '_logo', '');
        $this->session->set_flashdata('alert_success', lang($type . '_logo_removed'));
        redirect('settings');
    }
}
