<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Suppliers extends Admin_Controller
{
    /**
     * Suppliers constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_suppliers',
            'Mdl_payments',
            'Mdl_custom_fields',
            'Mdl_custom_fields_data',
            'users/Mdl_users',
            'suppliers/Mdl_supplier_notes',
            'invoices/Mdl_invoices',
            'quotes/Mdl_quotes'
        ]);
    }

    /**
     * Index action
     */
    public function index()
    {
        redirect('suppliers/status/active');
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
            $this->Mdl_suppliers->$function();
        }

        $suppliers = $this->Mdl_suppliers
            ->filter_where('ip_suppliers.company_id', $this->companyId)
            ->with_total_balance()
            ->get()
            ->result();

        $this->layout->set([
            'records'            => $suppliers,
            'filter_display'     => true,
            'filter_placeholder' => lang('filter_suppliers'),
            'filter_method'      => 'filter_suppliers'
        ]);

        $this->layout->buffer('content', 'suppliers/index');
        $this->layout->render();
    }

    /**
     * Edit form
     * @param null $id
     */
    public function form($id = null)
    {
        include APPPATH . '/libraries/iban/php-iban.php';

        if ($this->input->post('btn_cancel', true)) {
            redirect('suppliers');
        }

        if ($this->input->post('btn_submit', true)) {
            $_POST['company_id'] = $this->companyId;
            if ($this->Mdl_suppliers->run_validation()) {
                $id = $this->Mdl_suppliers->save($id);
                $this->Mdl_suppliers->save($id, ['supplier_created_by' => $this->userId]);
                $this->Mdl_custom_fields_data->save_custom($id, $this->input->post('custom', true), ' ip_supplier_custom');
                redirect('suppliers/view/' . $id);
            }
        }

        if ($id and !$this->input->post('btn_submit', true)) {
            if (!$this->Mdl_suppliers->prep_form($id)) {
                show_404();
            }

            $this->Mdl_suppliers->set_form_value('is_update', true);
            $supplier_custom = $this->Mdl_custom_fields->by_table('ip_supplier_custom', $id);

            if ($supplier_custom->num_rows()) {
                $supplier_custom = $supplier_custom->result_array();

                foreach ($supplier_custom as $key => $val) {
                    $this->Mdl_suppliers->set_form_value("custom[{$val['custom_field_column']}]", $val['value_data']);
                }
            }
        } elseif ($this->input->post('btn_submit', true)) {
            if ($this->input->post('custom', true)) {
                foreach ($this->input->post('custom', true) as $key => $val) {
                    $this->Mdl_suppliers->set_form_value("custom[{$key}]", $val);
                }
            }
        }

        $check = $this->Mdl_suppliers->getByPk($id);
        if ($id && $check->company_id != $this->companyId) {
            show_404();
        }

        $defaultCountry = $this->Mdl_settings->setting('default_country');

        $this->layout
            ->set('custom_fields', $this->Mdl_custom_fields->by_table('ip_supplier_custom', $id)->result())
            ->set('countries', get_country_list(lang('cldr')))
            ->set('selected_country', $this->Mdl_suppliers->form_value('supplier_country') ?: $defaultCountry)
            ->buffer('content', 'suppliers/form')
            ->render();
    }

    /**
     * View
     * @param $supplierId
     */
    public function view($supplierId)
    {
        $supplier = $this->Mdl_suppliers
            ->filter_where('ip_suppliers.company_id', $this->companyId)
            ->with_total()
            ->with_total_balance()
            ->with_total_paid()
            ->where('ip_suppliers.supplier_id', $supplierId)
            ->get()
            ->row();

        if (!$supplier) {
            show_404();
        }

        $supplierNotes = $this->Mdl_supplier_notes->where('supplier_id', $supplierId)->get()->result();
        $quotes = $this->Mdl_quotes->by_supplier($supplierId)->get()->result();
        $invoices = $this->Mdl_invoices
            ->by_supplier($supplierId)
            ->get()
            ->result();

        $this->layout->set([
            'supplier'         => $supplier,
            'supplier_notes'   => $supplierNotes,
            'user'             => $this->Mdl_users->where('ip_users.user_id', $supplier->supplier_created_by)->get()->row(),
            'invoices'         => $invoices,
            'quotes'           => $quotes,
            'custom_fields'    => $this->Mdl_custom_fields->by_table('ip_supplier_custom', $supplierId)->result(),
            'quote_statuses'   => $this->Mdl_quotes->statuses(),
            'invoice_statuses' => $this->Mdl_invoices->statuses(),
        ]);

        $this->layout->buffer([
            ['invoice_table', 'invoices/partial_invoice_table'],
            ['quote_table', 'quotes/partial_quote_table'],
            ['payment_table', 'payments/partial_payment_table'],
            ['partial_notes', 'suppliers/partial_notes'],
            ['content', 'suppliers/view']
        ]);

        $this->layout->render();
    }

    /**
     * Delete
     * @param $supplierId
     */
    public function delete($supplierId)
    {
        $this->Mdl_suppliers->delete($supplierId);
        redirect('suppliers');
    }

    public function import()
    {
        require_once(APPPATH . 'helpers/csv/parsecsv.lib.php');

        if ($this->input->post('btn_submit_1', true)) {
            if (empty($_FILES['fileUpload']['tmp_name'])) {
                $this->session->set_flashdata('alert_error', lang('error_select_file'));
                redirect('suppliers/import');
            }

            $ext = pathinfo($_FILES['fileUpload']['name'], PATHINFO_EXTENSION);

            if (strtolower($ext) != 'csv') {
                $this->session->set_flashdata('alert_error', lang('error_select_only_csv'));
                redirect('suppliers/import');
            }

            $target_path = $_FILES['fileUpload']['tmp_name'];
            $csv = new parseCSV();
            $header = $finalData = [];

            if ($this->input->post('import_has_header', true) == 1) {
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

            $mysql_headers = $this->Mdl_suppliers->get_cols_name_for_import();

            $this->layout->set([
                'csv_headers'         => $header,
                'mysql_cols'          => $mysql_headers,
                'csv_data'            => serialize($finalData),
                'import_has_header'   => $import_has_header,
                'action_on_duplicate' => $this->input->post('duplicate_rec', true)
            ]);

            $this->layout->buffer([
                ['content', 'suppliers/import_2']
            ]);

            $this->layout->render();

            return;
        }

        if ($this->input->post('btn_submit_2', true)) {
            $csv_data = unserialize($this->input->post('csv_data', true));
            $csv_headers = unserialize($this->input->post('csv_headers', true));
            $import_has_header = $this->input->post('import_has_header', true);
            $new_world_order = $this->input->post('new_world_order', true);
            $mysql_cols = unserialize($this->input->post('mysql_cols', true));
            $actionOnDuplicate = $this->input->post('action_on_duplicate', true);
            $suppliers_created = 0;
            $suppliers_updated = 0;
            $suppliers_ignored = 0;

            $new_world_order = explode(',', $new_world_order);

            $batch = 0;
            $db_array_supplier_final = [];

            foreach ($csv_data as $import_data) {
                $db_array_supplier = [];
                $db_array_supplier['company_id'] = $this->companyId;
                $temp_reg_number = '';

                foreach ($new_world_order as $kp => $p) {
                    if ((count($db_array_supplier) - 1) >= count($mysql_cols)) {
                        continue;
                    }

                    if ($p != '0' and isset($mysql_cols[$kp]['col'])) {
                        $db_array_supplier[$mysql_cols[$kp]['col']] = $import_data[array_search($p, $csv_headers)];
                    }

                    if ($mysql_cols[$kp]['col'] == 'supplier_reg_number') {
                        $temp_reg_number = $import_data[array_search($p, $csv_headers)];
                    }
                }

                $checkIfPresentSupplier = $this->Mdl_suppliers
                    ->where('supplier_reg_number', $temp_reg_number)
                    ->where('company_id', $this->companyId)
                    ->get();

                if ($checkIfPresentSupplier->num_rows()) {
                    if ($actionOnDuplicate == 'update') {
                        $supplier_id = $checkIfPresentSupplier->row()->supplier_id;
                        $this->Mdl_suppliers->save($supplier_id, $db_array_supplier);
                        $suppliers_updated++;
                    }

                    if ($actionOnDuplicate == 'ignore') {
                        $suppliers_ignored++;
                    }
                } else {
                    $db_array_supplier['supplier_date_created'] = date("Y-m-d H:i:s");
                    $db_array_supplier['supplier_date_modified'] = date("Y-m-d H:i:s");
                    $db_array_supplier_final[] = $db_array_supplier;
                    $suppliers_created++;
                }
            }

            if (count($db_array_supplier_final) > 0) {
                $this->db->insert_batch('ip_suppliers', $db_array_supplier_final);
            }

            $this->session->set_flashdata(
                'alert_success',
                sprintf(lang('supplier_imp_conf_msg'), $suppliers_created, $suppliers_updated, $suppliers_ignored)
            );

            redirect('suppliers/import');
        }

        $this->layout->buffer([
            ['content', 'suppliers/import_1']
        ]);

        $this->layout->render();
    }
}
