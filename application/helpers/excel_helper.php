<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function export_as_excel($params, $from_date, $to_date)
{
    $CI = &get_instance();
    $userName = $CI->session->userdata('user_name');
    $invoices = $params['invoices'];
    $export_type = $params['export_type'];
    $new_world_order = $params['new_world_order'];
    $extra_field_opt = $params['additional_options'];
    $invoice_statuses = $CI->Mdl_invoices->statuses();
    $currency_symbol = $CI->Mdl_settings->setting('currency_symbol');
    $params['mysql_cols'] = $CI->Mdl_invoices->get_cols_name_for_export();

    global $rowIndex;
    global $objPHPExcel;

    $CI->load->model([
        'Mdl_items',
        'Mdl_tax_rates',
        'Mdl_payment_methods',
        'Mdl_invoice_tax_rates',
        'Mdl_custom_fields',
        'Mdl_item_lookups'
    ]);

    error_reporting(E_ALL);
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);

    /** Include PHPExcel */
    require_once(APPPATH . 'helpers/excel/PHPExcel.php');

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator($userName)
        ->setLastModifiedBy($userName)
        ->setTitle("Office 2007 Invoices")
        ->setSubject("Invoices");

    $rowIndex = 1;
    set_excel_header($params);
    $rowIndex++;

    foreach ($invoices as $index => $invoice) {
        $index = 0;

        $objPHPExcel->getActiveSheet()->getStyle('A1:X1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()
            ->getStyle('A1:X1')
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $tax_rates = $CI->Mdl_tax_rates->filter_where('ip_tax_rates.company_id', $CI->session->userdata('company_id'))
            ->get()
            ->result();

        $payment_meth = $CI->Mdl_payment_methods->filter_where(
            'ip_payment_methods.company_id', $CI->session->userdata('company_id')
        )->get()->result();

        $items = $CI->Mdl_items->where('invoice_id', $invoice->invoice_id)->get()->result();
        $tax_rates = $tax_rates;//$this->Mdl_tax_rates->get()->result(),
        $invoice_tax_rates = $CI->Mdl_invoice_tax_rates->where('invoice_id', $invoice->invoice_id)->get()->result();
        $payment_methods = $payment_meth;//$this->Mdl_payment_methods->get()->result(),
        $custom_fields = $CI->Mdl_custom_fields->by_table('ip_invoice_custom')->result();
        $item_lookups = $CI->Mdl_item_lookups->get()->result();

        $startItemRow = $rowIndex;

        foreach ($items as $k => $item) {
            $item_tax_rate = lang('none');
            foreach ($tax_rates as $tax_rate) {
                if ($item->item_tax_rate_id == $tax_rate->tax_rate_id) {
                    $item_tax_rate = $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name;
                }
            }
            //invoice part
            set_excel_body($params, $invoice, $invoice_statuses, $item, $item_tax_rate);
            $rowIndex++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Invoices');
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
    }

    $objPHPExcel->setActiveSheetIndex(0);

    if ($export_type == 'excel') {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . lang('invoices') . '_' . join('_',
                [$from_date, $to_date]) . '.xlsx"');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    } elseif ($export_type == 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . lang('invoices') . '_' . join('_',
                [$from_date, $to_date]) . '.csv"');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
    }

    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter->save('php://output');
    exit;
}

function set_excel_header($params)
{
    global $rowIndex;
    global $objPHPExcel;
    global $CI;
    $currency_symbol = $CI->Mdl_settings->setting('currency_symbol');

    if ($params['additional_options'] != 1) {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $rowIndex, lang('date'))
            ->setCellValue('B' . $rowIndex, lang('invoice'))
            ->setCellValue('C' . $rowIndex, lang('status'))
            ->setCellValue('D' . $rowIndex, lang('client_name'))
            ->setCellValue('E' . $rowIndex, lang('email_address'))
            ->setCellValue('F' . $rowIndex, lang('date'))
            ->setCellValue('G' . $rowIndex, lang('due_date'))
            ->setCellValue('H' . $rowIndex, lang('subtotal'))
            ->setCellValue('I' . $rowIndex, lang('item_tax'))
            ->setCellValue('J' . $rowIndex, lang('invoice_tax'))
            ->setCellValue('K' . $rowIndex, lang('discount') . $currency_symbol)
            ->setCellValue('L' . $rowIndex, lang('discount') . "%")
            ->setCellValue('M' . $rowIndex, lang('total'))
            ->setCellValue('N' . $rowIndex, lang('total_paid'))
            ->setCellValue('O' . $rowIndex, lang('balance'))
            ->setCellValue('P' . $rowIndex, lang('item'))
            ->setCellValue('Q' . $rowIndex, lang('description'))
            ->setCellValue('R' . $rowIndex, lang('quantity'))
            ->setCellValue('S' . $rowIndex, lang('price'))
            ->setCellValue('T' . $rowIndex, lang('tax_rate'))
            ->setCellValue('U' . $rowIndex, lang('tax'))
            ->setCellValue('V' . $rowIndex, lang('subtotal'))
            ->setCellValue('W' . $rowIndex, lang('item_discount') . ' - ' . $currency_symbol)
            ->setCellValue('X' . $rowIndex, lang('total'));;
    } else {
        $row_index_letter = 65;
        foreach ($params['new_world_order'] as $row_index_c) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(
                chr($row_index_letter++) . $rowIndex, $params['mysql_cols'][$row_index_c]['name']
            );
        }
    }

    return $objPHPExcel;
}

function set_excel_body($params, $invoice, $invoice_statuses, $item, $item_tax_rate)
{
    global $rowIndex;
    global $objPHPExcel;
    global $CI;
    $currency_symbol = $CI->Mdl_settings->setting('currency_symbol');

    if ($params['additional_options'] != 1) {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $rowIndex, $invoice->invoice_date_created)
            ->setCellValue('B' . $rowIndex, $invoice->invoice_number)
            ->setCellValue('C' . $rowIndex, $invoice_statuses[$invoice->invoice_status_id]['label'])
            ->setCellValue('D' . $rowIndex, $invoice->client_name)
            ->setCellValue('E' . $rowIndex, $invoice->client_email)
            ->setCellValue('F' . $rowIndex, $invoice->invoice_date_created)
            ->setCellValue('G' . $rowIndex, $invoice->invoice_date_due)
            ->setCellValue('H' . $rowIndex, $invoice->invoice_item_subtotal)
            ->setCellValue('I' . $rowIndex, $invoice->invoice_item_tax_total)
            ->setCellValue('J' . $rowIndex, $invoice->invoice_item_tax_total)
            ->setCellValue('K' . $rowIndex, $invoice->invoice_discount_amount)
            ->setCellValue('L' . $rowIndex, $invoice->invoice_discount_percent)
            ->setCellValue('M' . $rowIndex, $invoice->invoice_total)
            ->setCellValue('N' . $rowIndex, $invoice->invoice_paid)
            ->setCellValue('O' . $rowIndex, $invoice->invoice_balance)
            ->setCellValue('P' . $rowIndex, $item->item_name)
            ->setCellValue('Q' . $rowIndex, $item->item_description)
            ->setCellValue('R' . $rowIndex, $item->item_quantity)
            ->setCellValue('S' . $rowIndex, $item->item_price)
            ->setCellValue('T' . $rowIndex, $item_tax_rate)
            ->setCellValue('U' . $rowIndex, $item->item_tax_total)
            ->setCellValue('V' . $rowIndex, $item->item_subtotal)
            ->setCellValue('W' . $rowIndex, $item->item_discount_amount)
            ->setCellValue('X' . $rowIndex, $item->item_total);
    } else {
        $row_index_letter = 65;
        foreach ($params['new_world_order'] as $row_index_c) {

            $data_to_fill = '';
            if ($params['mysql_cols'][$row_index_c]['type'] == 'Item' && $params['mysql_cols'][$row_index_c]['col'] == 'item_tax_rate') {
                $data_to_fill = ${$params['mysql_cols'][$row_index_c]['col']};
            } else {
                if ($params['mysql_cols'][$row_index_c]['type'] == 'Invoice' && $params['mysql_cols'][$row_index_c]['col'] == 'invoice_status_id') {
                    $data_to_fill = $invoice_statuses[$invoice->invoice_status_id]['label'];
                } else {
                    $data_to_fill = ${strtolower($params['mysql_cols'][$row_index_c]['type'])}->{$params['mysql_cols'][$row_index_c]['col']};
                }
            }

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($row_index_letter++) . $rowIndex, $data_to_fill);
        }
    }

    return $objPHPExcel;
}
