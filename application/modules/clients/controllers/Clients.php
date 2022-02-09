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
            'users/Mdl_users',
            'invoices/Mdl_invoices',
            'quotes/Mdl_quotes',
            'Mdl_payments',
            'Mdl_tasks',
            'email/Mdl_email'
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
     * By status
     * @param string $status
     * @param int $page
     */
    public function status($status = 'active', $page = 0)
    {

        if (is_numeric(array_search($status, ['active', 'inactive']))) {
            $function = 'is_' . $status;
            $this->Mdl_clients->$function();
        }

        $clients = $this->Mdl_clients
            ->filter_where('ip_clients.company_id', $this->companyId)
            ->with_total_balance()
            ->get()
            ->result();

        $this->layout->set([
            'records'            => $clients,
            'filter_display'     => true,
            'filter_placeholder' => lang('filter_clients'),
            'filter_method'      => 'filter_clients'
        ]);

        $this->layout->buffer('content', 'clients/index');
        $this->layout->render();
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

        $check = $this->Mdl_clients->getByPk($id);
        if ($id && $check->company_id != $this->companyId) {
            show_404();
        }

        if ($this->input->post('btn_submit', true)) {

            $_POST['client_id'] = $id;
            $_POST['company_id'] = $this->companyId;

            if ($this->Mdl_clients->run_validation()) {
                $id = $this->Mdl_clients->save($id);
                $this->Mdl_clients->save($id, ['client_created_by' => $this->userId]);
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
        } else if ($this->input->post('btn_submit', true)) {
            if ($this->input->post('is', true)) {
                foreach ($this->input->post('custom', true) as $key => $val) {
                    $this->Mdl_clients->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        $this->layout->set([
            'custom_fields'    => $this->Mdl_custom_fields->by_table('ip_client_custom', $id)->result(),
            'countries'        => get_country_list(lang('cldr')),
            'selected_country' => $this->Mdl_clients->form_value('client_country') ?
                $this->Mdl_clients->form_value('client_country') :
                $this->Mdl_settings->setting('default_country')
        ]);

        $this->layout->buffer('content', 'clients/form');
        $this->layout->render();
    }

    /**
     * View
     * @param $clientId
     */
    public function view($clientId)
    {
        $this->Mdl_clients->filter_where('ip_clients.company_id', $this->companyId);
        $client = $this->Mdl_clients
            ->with_total()
            ->with_total_balance()
            ->with_total_paid()
            ->where('ip_clients.client_id', $clientId)
            ->get()
            ->row();

        if (!$client) {
            show_404();
        }

        $payments = $this->db->get_where('ip_payments', ['company_id' => $this->companyId])->result_array();
        foreach ($payments as $p) {
            $paymentClientId = $this->db->get_where('ip_invoices', ['invoice_id' => $p['invoice_id']])->row('client_id');
            if ($paymentClientId == $clientId) {
                $invoices[] = $p;
            }
        }

        $this->layout->set([
            'client'           => $client,
            'client_notes'     => $this->Mdl_client_notes->where('client_id', $clientId)->get()->result(),
            'user'             => $this->Mdl_users->where('ip_users.user_id', $client->client_created_by)->get()->row(),
            'invoices'         => $this->Mdl_invoices->by_client($clientId)->get()->result(),
            'quotes'           => $this->Mdl_quotes->by_client($clientId)->get()->result(),
            'payments'         => $this->Mdl_payments->getPayments($clientId),
            'custom_fields'    => $this->Mdl_custom_fields->by_table('ip_client_custom', $clientId)->result(),
            'quote_statuses'   => $this->Mdl_quotes->statuses(),
            'invoice_statuses' => $this->Mdl_invoices->statuses(),
            'tasks'            => $this->Mdl_tasks->get_task_by_id($clientId),
            'inbox'            => $this->Mdl_email->clients_inbox($clientId),
            'outbox'           => $this->Mdl_email->clients_outbox($clientId)
        ]);

        $this->layout->buffer([
            ['invoice_table', 'invoices/partial_client_invoice_table'],
            ['quote_table', 'quotes/partial_quote_table'],
            ['payment_table', 'payments/partial_payment_table'],
            ['partial_notes', 'clients/partial_notes'],
            ['tasks_table', 'clients/tasks_table'],
            ['content', 'clients/view']
        ]);

        $this->layout->render();
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
     * Import
     */
    public function import()
    {
        require_once(APPPATH . 'helpers/csv/parsecsv.lib.php');

        if ($this->input->post('btn_submit', true)) {
            if (empty($_FILES['fileUpload']['tmp_name'])) {
                $this->session->set_flashdata('alert_error', lang('error_select_file'));
                redirect('clients/import');
            }

            $ext = pathinfo($_FILES['fileUpload']['name'], PATHINFO_EXTENSION);
            if (strtolower($ext) != 'csv') {
                $this->session->set_flashdata('alert_error', lang('error_select_only_csv'));
                redirect('clients/import');
            }

            $target_path = $_FILES['fileUpload']['tmp_name'];

            $csv = new parseCSV();
            $header = $finalData = [];

            if ($this->input->post('import_has_header', true) == "on") {

                $csv->heading = true;
                $csv->auto($target_path);
                $import_has_header = 1;

                if (count($csv->data[0]) == 1) {
                    $csv->encoding('UTF-16', 'UTF-8');
                    $csv->auto($target_path);
                }

                foreach ($csv->data[0] as $k => $item) {
                    $header[] = addslashes($k);
                }
                foreach ($csv->data as $k1 => $row) {
                    $temp = [];
                    foreach ($row as $k2 => $cell) {
                        $temp[] = addslashes($cell);
                    }
                    $finalData[] = $temp;
                }

            } else {
                $i = 1;
                $csv->heading = false;
                $csv->auto($target_path);
                $import_has_header = 0;

                if (count($csv->data[0]) == 1) {
                    $csv->encoding('UTF-16', 'UTF-8');
                    $csv->auto($target_path);
                }

                foreach ($csv->data[0] as $k => $item) {
                    $header[] = 'col-' . $i;
                    $i++;
                }
                foreach ($csv->data as $k1 => $row) {
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

        if ($this->input->post('btn_submit_2')) {
            $csv_data = unserialize($this->input->post('csv_data', true));
            $csv_headers = unserialize($this->input->post('csv_headers', true));
            $import_has_header = $this->input->post('import_has_header', true);
            $new_world_order = $this->input->post('new_world_order', true);
            $mysql_cols = unserialize($this->input->post('mysql_cols', true));
            $action_on_duplicate = $this->input->post('action_on_duplicate', true);
            $clients_created = 0;
            $clients_updated = 0;
            $clients_ignored = 0;

            $new_world_order = explode(',', $new_world_order);

            $batch = 0;


            if($import_has_header == 1){
                $mmm = 0;
                foreach ($csv_data as $k => $row) {
                    $new_index = 0;
                    foreach ($mysql_cols as $kk => $mysql_row) {
                        if($mmm == 0) {
                            $new_csv_headers[] = $mysql_row['name'];
                        }
                        if (in_array($mysql_row['name'], $csv_headers)) {
                            $new_CSV[$mmm][] = $row[$new_index];
                            $new_index++;
                        } else {
                            $new_CSV[$mmm][] = "";
                        }

                    }
                    $mmm++;
                }

                $csv_data = $new_CSV;
                $csv_headers = $new_csv_headers;
                $new_world_order = $new_csv_headers;
            }


            $db_array_client_final = [];

            foreach ($csv_data as $import_data) {
                $db_array_client = [];
                $db_array_client['company_id'] = $this->companyId;

                $temp_reg_number = '';

                foreach ($new_world_order as $kp => $p) {
                    if ((count($db_array_client) - 1) >= count($mysql_cols)) {
                        continue;
                    }

                    if ($p != '0' and isset($mysql_cols[$kp]['col'])) {
                        $db_array_client[$mysql_cols[$kp]['col']] = $import_data[array_search($p, $csv_headers)];
                    }

                    if ($mysql_cols[$kp]['col'] == 'client_reg_number') {
                        $temp_reg_number = $import_data[array_search($p, $csv_headers)];
                    }
                }

                $check_if_present_client = $this->Mdl_clients
                    ->where('client_reg_number', $temp_reg_number)
                    ->where('company_id', $this->companyId)
                    ->get();

                if ($check_if_present_client->num_rows()) {
                    if ($action_on_duplicate == 'update') {
                        $client_id = $check_if_present_client->row()->client_id;
                        $this->Mdl_clients->save($client_id, $db_array_client);
                        $clients_updated++;
                    }

                    if ($action_on_duplicate == 'ignore') {
                        $clients_ignored++;
                    }

                } else {
                    $db_array_client['client_date_created'] = date("Y-m-d H:i:s");
                    $db_array_client['client_date_modified'] = date("Y-m-d H:i:s");
                    $db_array_client_final[] = $db_array_client;

                    $clients_created++;
                }
            }

            if (count($db_array_client_final) > 0) {
                $this->db->insert_batch('ip_clients', $db_array_client_final);
            }

            $this->session->set_flashdata(
                'alert_success', sprintf(
                    lang('client_imp_conf_msg'), $clients_created, $clients_updated, $clients_ignored
                )
            );

            redirect('clients/import');
        }

        $this->layout->buffer([
            ['content', 'clients/import_1']
        ]);

        $this->layout->render();
    }
}
