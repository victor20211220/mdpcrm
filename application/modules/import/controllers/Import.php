<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Import extends Admin_Controller
{
    private $allowedFiles = [
        0 => 'clients.csv',
        1 => 'invoices.csv',
        2 => 'invoice_items.csv',
        3 => 'payments.csv'
    ];

    /**
     * Import constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_import',
            'invoices/Mdl_invoices',
            'invoices/Mdl_items',
            'Mdl_payments'
        ]);
    }

    /**
     * Index action
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->Mdl_import->paginate(site_url('import/index'), $page);
        $imports = $this->Mdl_import->result();

        $this->layout
            ->set('imports', $imports)
            ->buffer('content', 'import/index')
            ->render();
    }

    /**
     * Form
     */
    public function form()
    {
        if (!$this->input->post('btn_submit', true)) {
            $files = directory_map('./uploads/import');

            foreach ($files as $key => $file) {
                if (!is_numeric(array_search($file, $this->allowedFiles))) {
                    unset($files[$key]);
                }
            }

            $this->layout
                ->set('files', $files)
                ->buffer('content', 'import/import_index')
                ->render();
        } else {
            $importId = $this->Mdl_import->start_import();

            if ($this->input->post('files', true)) {
                $files = $this->allowedFiles;

                foreach ($files as $key => $file) {
                    if (!is_numeric(array_search($file, $this->input->post('files', true)))) {
                        unset($files[$key]);
                    }
                }

                foreach ($files as $file) {
                    if ($file == 'clients.csv') {
                        $ids = $this->Mdl_import->import_data($file, 'ip_clients');
                        $this->Mdl_import->record_import_details($importId, 'ip_clients', 'clients', $ids);
                    } elseif ($file == 'invoices.csv') {
                        $ids = $this->Mdl_import->import_invoices();
                        $this->Mdl_import->record_import_details($importId, 'ip_invoices', 'invoices', $ids);
                    } elseif ($file == 'invoice_items.csv') {
                        $ids = $this->Mdl_import->import_invoice_items();
                        $this->Mdl_import->record_import_details($importId, 'ip_invoice_items', 'invoice_items', $ids);
                    } elseif ($file == 'payments.csv') {
                        $ids = $this->Mdl_import->import_payments();
                        $this->Mdl_import->record_import_details($importId, 'ip_payments', 'payments', $ids);
                    }
                }
            }

            redirect('import');
        }
    }

    /**
     * Delete
     * @param $id
     */
    public function delete($id)
    {
        $this->Mdl_import->delete($id);
        redirect('import');
    }
}
