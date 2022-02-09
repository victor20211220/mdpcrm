<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once(APPPATH . 'helpers/mpdf/mpdf.php');

function export_as_zip_pdf($params, $from_date, $to_date)
{
    $CI = &get_instance();
    $userName = $CI->session->userdata('user_name');
    $company_id = $CI->session->userdata('company_id');
    $invoices = $params['invoices'];
    $export_type = $params['export_type'];
    $invoice_statuses = $CI->Mdl_invoices->statuses();
    $currency_symbol = $CI->Mdl_settings->setting('currency_symbol');

    error_reporting(E_ALL);
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);


    $CI->load->model('invoices/Mdl_invoices');
    $CI->load->model('invoices/Mdl_items');
    $CI->load->model('invoices/Mdl_invoice_tax_rates');
    $CI->load->model('Mdl_payment_methods');
    $CI->load->library('encrypt');
    $CI->load->helper('pdf');
    $CI->load->helper('qr');

    $pdf_files = array();

    foreach ($invoices as $invoice) {

        $invoiceId = $invoice->invoice_id;
        $result_arr = generate_invoice_loop($invoiceId);

        $Directory = $result_arr['zip'];
        $pdf_files[] = $result_arr['dtl']['path'].$result_arr['dtl']['filename'];

    }

    $zipFilePAth = $Directory . date('Y-m-d') . '_invoices.zip';
    $result = create_zip($pdf_files, $zipFilePAth);

    header('Content-type: application/zip');
    header('Content-Disposition: attachment; filename="' . lang('invoices') . '_' . join('_', [$from_date, $to_date]) . '.zip' . '"');
    readfile($zipFilePAth);

    $interval = 3600;
    if ($handle = @opendir(preg_replace('/\/$/', '', './uploads/zip_exp/' . $company_id . '/'))) {
        while (false !== ($file = readdir($handle))) {
            { // mPDF 5.7.3
                unlink($Directory . $file);
            }
        }
        closedir($handle);
    }
    exit;
}

function create_zip($files = [], $destination = '', $overwrite = false)
{
    //if the zip file already exists and overwrite is false, return false
    if (file_exists($destination) && !$overwrite) {
        return false;
    }
    //vars
    $valid_files = [];
    //if files were passed in...
    if (is_array($files)) {
        //cycle through each file
        foreach ($files as $file) {
            //make sure the file exists
            if (file_exists($file)) {
                $valid_files[] = $file;
            }
        }
    }
    //if we have good files...
    if (count($valid_files)) {
        //create the archive
        $zip = new ZipArchive();
        if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        //add the files
        foreach ($valid_files as $file) {
            $zip->addFile($file, basename($file));
        }

        $zip->close();

        //check to make sure the file exists
        return file_exists($destination);
    } else {
        return false;
    }
}
