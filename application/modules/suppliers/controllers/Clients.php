<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients extends Admin_Controller
{
    /**
     * Clients constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_clients',
            'Mdl_custom_fields',
            'Mdl_custom_fields_data',
            'Mdl_client_notes',
            'invoices/Mdl_invoices',
            'quotes/Mdl_quotes',
            'Mdl_payments'
        ]);
    }

    /**
     * Index action
     */
    public function index()
    {
        redirect('clients/status/active');
    }

    /**
     * List by status
     * @param string $status
     */
    public function status($status = 'active')
    {
        if (is_numeric(array_search($status, ['active', 'inactive']))) {
            $function = 'is_' . $status;
            $this->Mdl_clients->$function();
        }

        $this->Mdl_clients->filter_where('ip_clients.company_id', $this->companyId);
        $clients = $this->Mdl_clients->with_total_balance()->get()->result();

        $this->layout->set([
            'records'            => $clients,
            'filter_display'     => true,
            'filter_placeholder' => lang('filter_clients'),
            'filter_method'      => 'filter_clients'
        ])
            ->buffer('content', 'clients/index')
            ->render();
    }

    /**
     * Form
     * @param null $id
     */
    public function form($id = null)
    {
        include APPPATH . '/libraries/iban/php-iban.php';

        if ($this->input->post('btn_cancel', true)) {
            redirect('clients');
        }

        if ($this->input->post('btn_submit', true)) {
            $_POST['company_id'] = $this->companyId;
            if ($this->Mdl_clients->run_validation()) {
                $id = $this->Mdl_clients->save($id);
                $this->Mdl_custom_fields_data->save_custom($id, $this->input->post('custom', true), 'ip_client_custom');
                redirect('clients/view/' . $id);
            }
        }

        if ($id and !$this->input->post('btn_submit', true)) {
            if (!$this->Mdl_clients->prep_form($id)) {
                show_404();
            }

            $this->Mdl_clients->set_form_value('is_update', true);
            $client_custom = $this->Mdl_custom_fields->by_table('ip_client_custom', $id);

            if ($client_custom->num_rows()) {
                $client_custom = $client_custom->result_array();
                foreach ($client_custom as $key => $val) {
                    $this->Mdl_clients->set_form_value("custom['{$val['custom_field_column']}']", $val['value_data']);
                }
            }
        } elseif ($this->input->post('btn_submit', true)) {
            if ($this->input->post('custom', true)) {
                foreach ($this->input->post('custom', true) as $key => $val) {
                    $this->Mdl_clients->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        $check = $this->Mdl_clients->get_by_id($id);
        if ($id && $check->company_id != $this->companyId) {
            show_404();
        }

        $this->layout->set('custom_fields', $this->Mdl_custom_fields->by_table('ip_client_custom', $id)->result());
        $this->layout->set('countries', get_country_list(lang('cldr')));
        $this->layout->set('selected_country', $this->Mdl_clients->form_value('client_country') ?: $this->Mdl_settings->setting('default_country'));
        $this->layout->buffer('content', 'clients/form');
        $this->layout->render();
    }

    /**
     * View
     * @param $clientId
     */
    public function view($clientId)
    {
        $client = $this->Mdl_clients
            ->filter_where('ip_clients.company_id', $this->companyId)
            ->with_total()
            ->with_total_balance()
            ->with_total_paid()
            ->where('ip_clients.client_id', $clientId)
            ->get()
            ->row();

        if (!$client) {
            show_404();
        }

        $clientNotes = $this->Mdl_client_notes->where('client_id', $clientId)->get()->result();
        $clientInvoices = $this->Mdl_invoices
            ->by_client($clientId)
            ->filter_where('ip_invoices.is_received', 0)
            ->get()
            ->result();

        $this->layout->set([
            'client'           => $client,
            'client_notes'     => $clientNotes,
            'invoices'         => $clientInvoices,
            'quotes'           => $this->Mdl_quotes->by_client($clientId)->get()->result(),
            'payments'         => $this->Mdl_payments->by_client($clientId)->get()->result(),
            'custom_fields'    => $this->Mdl_custom_fields->by_table('ip_client_custom', $clientId)->result(),
            'quote_statuses'   => $this->Mdl_quotes->statuses(),
            'invoice_statuses' => $this->Mdl_invoices->statuses(),
        ])
            ->buffer([
            ['invoice_table', 'invoices/partial_invoice_table'],
            ['quote_table', 'quotes/partial_quote_table'],
            ['payment_table', 'payments/partial_payment_table'],
            ['partial_notes', 'clients/partial_notes'],
            ['content', 'clients/view']
        ])
            ->render();
    }

    /**
     * Delete
     * @param $clientId
     */
    public function delete($clientId)
    {
        $this->Mdl_clients->delete($clientId);
        redirect('clients');
    }

    /**
     * Import clients
     */
    public function import()
    {
        require_once(APPPATH . 'helpers/csv/parsecsv.lib.php');

        if ($this->input->post('btn_submit_1', true)) {
            if (empty($_FILES['fileUpload']['tmp_name'])) {
                $this->session->set_flashdata('alert_error', lang('error_select_file'));
                redirect('clients/import');
            }

            $ext = pathinfo($_FILES['fileUpload']['name'], PATHINFO_EXTENSION);

            if (strtolower($ext) != 'csv') {
                $this->session->set_flashdata('alert_error', lang('error_select_only_csv'));
                redirect('clients/import');
            }

            $targetPath = $_FILES['fileUpload']['tmp_name'];
            $parser = new parseCSV();
            $header = $finalData = [];

            //get file header

            if ($this->input->post('import_has_header', true) == 1) {
                $parser->heading = true;
                $parser->auto($targetPath);
                $import_has_header = 1;

                if (count($parser->data[0]) == 1) {
                    $parser->encoding('UTF-16', 'UTF-8');
                    $parser->auto($targetPath);
                }

                foreach ($parser->data[0] as $k => $item) {
                    $header[] = addslashes($k);
                }

                foreach ($parser->data as $k1 => $row) {
                    $temp = [];
                    foreach ($row as $k2 => $cell) {
                        $temp[] = addslashes($cell);
                    }

                    $finalData[] = $temp;
                }
            } else {
                $i = 1;
                $parser->heading = false;
                $parser->auto($targetPath);
                $import_has_header = 0;

                if (count($parser->data[0]) == 1) {
                    $parser->encoding('UTF-16', 'UTF-8');
                    $parser->auto($targetPath);
                }

                foreach ($parser->data[0] as $k => $item) {
                    $header[] = 'col-' . $i;
                    $i++;
                }

                foreach ($parser->data as $k1 => $row) {
                    $temp = [];
                    foreach ($row as $k2 => $cell) {
                        $temp[] = addslashes($cell);
                    }

                    $finalData[] = $temp;
                }
            }

            $this->layout->set([
                'csv_headers'         => $header,
                'mysql_cols'          => $this->Mdl_clients->get_cols_name_for_import(),
                'csv_data'            => serialize($finalData),
                'import_has_header'   => $import_has_header,
                'action_on_duplicate' => $this->input->post('duplicate_rec', true)
            ]);

            $this->layout->buffer([
                ['content', 'clients/import_2']
            ]);

            $this->layout->render();

            return;
        }

        if ($this->input->post('btn_submit_2', true)) {
            $csvData = unserialize($this->input->post('csv_data', true));
            $csvHeaders = unserialize($this->input->post('csv_headers', true));
            $newWorldOrder = $this->input->post('new_world_order', true);
            $mysqlCols = unserialize($this->input->post('mysql_cols', true));
            $actionOnDuplicate = $this->input->post('action_on_duplicate', true);
            $clientsCreated = 0;
            $clientsUpdated = 0;
            $clientsIgnored = 0;
            $newWorldOrder = explode(',', $newWorldOrder);

            $arrayBatch = [];

            foreach ($csvData as $data) {
                $array = [];
                $array['company_id'] = $this->companyId;
                $tempRegNumber = '';

                foreach ($newWorldOrder as $kp => $p) {
                    if ((count($array) - 1) >= count($mysqlCols)) {
                        continue;
                    }

                    if ($p != '0' and isset($mysqlCols[$kp]['col'])) {
                        $array[$mysqlCols[$kp]['col']] = $data[array_search($p, $csvHeaders)];
                    }

                    if ($mysqlCols[$kp]['col'] == 'client_reg_number') {
                        $tempRegNumber = $data[array_search($p, $csvHeaders)];
                    }
                }

                $checkIfPresentClient = $this->Mdl_clients
                    ->where('client_reg_number', $tempRegNumber)
                    ->where('company_id', $this->companyId)
                    ->get();

                if ($checkIfPresentClient->num_rows()) {
                    if ($actionOnDuplicate == 'update') {
                        $client_id = $checkIfPresentClient->row()->client_id;
                        $this->Mdl_clients->save($client_id, $array);
                        $clientsUpdated++;
                    }

                    if ($actionOnDuplicate == 'ignore') {
                        $clientsIgnored++;
                    }
                } else {
                    $array['client_date_created'] = date("Y-m-d H:i:s");
                    $array['client_date_modified'] = date("Y-m-d H:i:s");
                    $arrayBatch[] = $array;
                    $clientsCreated++;
                }
            }

            if (count($arrayBatch) > 0) {
                $this->db->insert_batch('ip_clients', $arrayBatch);
            }

            $this->session->set_flashdata(
                'alert_success',
                sprintf(lang('client_imp_conf_msg'), $clientsCreated, $clientsUpdated, $clientsIgnored)
            );

            redirect('clients/import');
        }

        $this->layout->buffer([
            ['content', 'clients/import_1']
        ])->render();
    }
}
