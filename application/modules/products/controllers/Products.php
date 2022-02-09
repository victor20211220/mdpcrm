<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends Admin_Controller
{
    /**
     * Products constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_products',
            'Mdl_families',
            'Mdl_tax_rates',
            'Mdl_families',
            'Mdl_tax_rates'
        ]);
    }

    /**
     * Index action
     * @param int $page
     */
    public function index($page = 0)
    {
        $products = $this->Mdl_products
            ->filter_where('ip_products.company_id', $this->companyId)
            ->get()
            ->result();

        $this->layout
            ->set('products', $products)
            ->buffer('content', 'products/index')
            ->render();
    }

    public function form($id = null)
    {
        if ($this->input->post('btn_cancel', true)) {
            redirect('products');
        }

        if ($this->input->post('btn_submit', true)) {
            $_POST['product_id'] = $id;
            $_POST['company_id'] = $this->companyId;
            $_POST['product_search'] = ProductHelper::getSearchString($_POST['product_name']);

            if ($this->Mdl_products->run_validation()) {
                $this->Mdl_products->save($id);
                redirect('products');
            }
        }

        if ($id and !$this->input->post('btn_submit', true)) {
            if (!$this->Mdl_products->prep_form($id)) {
                show_404();
            }
        }

        $check = $this->Mdl_products->get_by_id($id);
        if ($id && $check->company_id != $this->companyId) {
            show_404();
        }

        $families = $this->Mdl_families
            ->filter_where('ip_families.company_id', $this->companyId)
            ->get()
            ->result();

        $taxRates = $this->Mdl_tax_rates
            ->filter_where('ip_tax_rates.company_id', $this->companyId)
            ->get()
            ->result();

        $this->layout->set([
            'families'  => $families,
            'tax_rates' => $taxRates
        ]);

        $this->layout
            ->buffer('content', 'products/form')
            ->render();
    }

    /**
     * Delete action
     * @param $id
     */
    public function delete($id)
    {
        $this->Mdl_products->delete($id);
        redirect('products');
    }

    public function import()
    {
        require_once(APPPATH . 'helpers/csv/parsecsv.lib.php');

        //check if we are at step 1
        if ($this->input->post('btn_submit_1', true)) {
            if (empty($_FILES['fileUpload']['tmp_name'])) {
                $this->session->set_flashdata('alert_error', 'Please select one file for import');
                redirect('clients/import');
            }

            $ext = pathinfo($_FILES['fileUpload']['name'], PATHINFO_EXTENSION);
            if (strtolower($ext) != 'csv') {
                $this->session->set_flashdata('alert_error', 'Only csv file format will be accepted');
                redirect('clients/import');
            }

            $target_path = $_FILES['fileUpload']['tmp_name'];
            $csv = new parseCSV();
            $header = $finalData = [];

            
            //get file header
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

            $this->layout
                ->set([
                    'csv_headers'         => $header,
                    'mysql_cols'          => $this->Mdl_products->getHeaderNamesForImport(),
                    'csv_data'            => serialize($finalData),
                    'import_has_header'   => $import_has_header,
                    'prod_family'         => $this->input->post('prod_family', true),
                    'prod_tax_cond'       => $this->input->post('prod_tax_cond', true),
                    'action_on_duplicate' => $this->input->post('duplicate_rec', true)
                ])
                ->buffer([
                    [
                        'content',
                        'products/import_2'
                    ]
                ])
                ->render();

            return;
        }
        //check for step 2
        if ($this->input->post('btn_submit_2', true)) {

            $import_has_header = $this->input->post('import_has_header', true);
            $csv_data = unserialize($this->input->post('csv_data', true));

            $csv_headers = unserialize($this->input->post('csv_headers', true));
            $new_world_order = $this->input->post('new_world_order', true);
            $mysql_cols = unserialize($this->input->post('mysql_cols', true));
            $action_on_duplicate = $this->input->post('action_on_duplicate', true);
            $prod_family = $this->input->post('prod_family', true);
            $prod_tax_cond = $this->input->post('prod_tax_cond', true);
            $products_created = 0;
            $products_updated = 0;
            $products_ignored = 0;

            $new_world_order = explode(',', $new_world_order);
            //loop through import data
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




            $db_array_products_final = [];

            foreach ($csv_data as $import_data) {

                //$db_array_client = array();
                $db_array_products = [];
                $db_array_products['company_id'] = $this->session->userdata('company_id');
                $temp_sku = '';
                $temp_fam_name = '';

                foreach ($new_world_order as $kp => $p) {


                    if (!isset($mysql_cols[$kp]['col'])) {
                        continue;
                    }


                    //loop through mysql cols
                    if ($p != '0' and isset($mysql_cols[$kp]['col'])) {
                        $db_array_products[$mysql_cols[$kp]['col']] = $import_data[array_search($p, $csv_headers)];
                    }

                    //save sku of product
                    if ($mysql_cols[$kp]['col'] == 'product_sku') {
                        $temp_sku = $import_data[array_search($p, $csv_headers)];
                    }

                    //check
                    if ($mysql_cols[$kp]['col'] == 'product_price' || $mysql_cols[$kp]['col'] == 'purchase_price') {
                        $db_array_products[$mysql_cols[$kp]['col']] = (float)preg_replace("/[^0-9\.]/", "",
                            $import_data[array_search($p, $csv_headers)]);
                    }

                    //check
                    if ($mysql_cols[$kp]['col'] == 'stock' || $mysql_cols[$kp]['col'] == 'stock_alert') {
                        $db_array_products[$mysql_cols[$kp]['col']] = preg_replace("/[^0-9]/", "",
                            $import_data[array_search($p, $csv_headers)]);
                    }

                    //****************************************
                    //set default tax rate
                    if ($mysql_cols[$kp]['col'] == 'tax_rate_id') {
                        $temp_tax_rate = $import_data[array_search($p, $csv_headers)];
                        $rate_percent = (float)preg_replace("/[^0-9\,\.]/", "", $temp_tax_rate);
                        $check_if_tax_rate = $this->Mdl_tax_rates->where('tax_rate_percent',
                            $rate_percent)->where('company_id', $this->session->userdata('company_id'))->get();

                        if ($check_if_tax_rate->num_rows()) {
                            $tax_rate_id = $check_if_tax_rate->row()->tax_rate_id;
                            $db_array_products[$mysql_cols[$kp]['col']] = $tax_rate_id;
                        } else {
                            if ($prod_tax_cond == 'create') {
                                $tax_rate_id = $this->Mdl_tax_rates->save(null, [
                                    'company_id'       => $this->session->userdata('company_id'),
                                    'tax_rate_percent' => $rate_percent,
                                    'tax_rate_name'    => $rate_percent . ' %'
                                ]);
                                $db_array_products[$mysql_cols[$kp]['col']] = $tax_rate_id;
                            }

                            if ($prod_tax_cond == 'ignore') {
                                $db_array_products[$mysql_cols[$kp]['col']] = -1;
                            }
                        }
                    }
                    //get id of family OR create familiy or set 01 family
                    //check family
                    if ($mysql_cols[$kp]['col'] == 'family_id') {
                        $temp_fam_name = $import_data[array_search($p, $csv_headers)];
                        $check_if_family = $this->Mdl_families->where('LOWER(family_name)',
                            strtolower($temp_fam_name))->where('company_id',
                            $this->session->userdata('company_id'))->get();

                        if ($check_if_family->num_rows()) {
                            $family_id = $check_if_family->row()->family_id;
                            $db_array_products[$mysql_cols[$kp]['col']] = $family_id;
                        } else {

                            if ($prod_family == 'create') {
                                $family_id = $this->Mdl_families->save(null, [
                                    'company_id'  => $this->session->userdata('company_id'),
                                    'family_name' => $import_data[array_search($p, $csv_headers)]
                                ]);
                                $db_array_products[$mysql_cols[$kp]['col']] = $family_id;
                            }

                            if ($prod_family == 'ignore') {
                                $db_array_products[$mysql_cols[$kp]['col']] = -1;
                            }
                        }
                    }
                }

                //create or update product
                $check_if_product = $this->Mdl_products->where('LOWER(product_sku)',
                    strtolower($temp_sku))->where('ip_products.company_id',
                    $this->session->userdata('company_id'))->get();

                if ($check_if_product->num_rows()) {

                    if ($action_on_duplicate == 'update') {
                        $product_id = $check_if_product->row()->product_id;
                        $this->Mdl_products->save($product_id, $db_array_products);
                        $products_updated++;
                    }
                    if ($action_on_duplicate == 'ignore') {
                        $products_ignored++;
                    }
                } else {

                    $db_array_products_final[] = $db_array_products;
                    $products_created++;

                }

            }

            if (count($db_array_products_final) > 0) {
                $this->db->insert_batch('ip_products', $db_array_products_final);
            }

            $this->session->set_flashdata('alert_success',
                $products_created . ' new products imported! ' . $products_updated . ' products updated! ' . $products_ignored . ' products ignored!');

            redirect('products/import');

        }

        //show first page
        $this->layout->buffer([
            ['content', 'products/import_1']
        ]);

        $this->layout->render();
    }
}
