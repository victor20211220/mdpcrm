<?php

use mikehaertl\wkhtmlto\Pdf;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

//function generate_invoice_loop($invoiceId, $invoiceTemplate = null)
//{
//    $CI = &get_instance();
//
//    $CI->load->model('invoices/mdl_invoices');
//    $CI->load->model('invoices/mdl_items');
//    $CI->load->model('invoices/mdl_invoice_tax_rates');
//    $CI->load->model('mdl_payment_methods');
//    //$CI->load->library('encrypt');
//    //$CI->load->library('towords');
//    $CI->load->helper('qr');
//
//    $invoice = $CI->mdl_invoices->get_by_id($invoiceId);
//
//    if (!$invoiceTemplate) {
//        $invoiceTemplate = 'default';
//    }
//
//    $paymentMethod = $CI->mdl_payment_methods
//        ->where('payment_method_id', $invoice->payment_method)
//        ->get()
//        ->row();
//
//    if ($invoice->payment_method == 0) {
//        $paymentMethod = null;
//    }
//
//    $qr_code_file_path = generate_qr_code($invoice);
//
//    if ($invoice->is_received == 1) {
//        $invoice = reverse_details_rec_invoices($invoice);
//    }
//
//    $items = $CI->mdl_items->where('invoice_id', $invoiceId)->get()->result();
//    $itemsDiscount = false;
//    foreach ($items as $i) {
//        if ($i->item_discount_percent > 0) {
//            $itemsDiscount = true;
//            break;
//        }
//    }
//
//    $data = [
//        'qr_code_file_path' => $qr_code_file_path,
//        'invoice'           => $invoice,
//        'invoice_tax_rates' => $CI->mdl_invoice_tax_rates->where('invoice_id', $invoiceId)->get()->result(),
//        'items'             => $items,
//        'itemsDiscount'     => $itemsDiscount,
//        'payment_method'    => $paymentMethod,
//        'output_type'       => 'pdf'
//    ];
//
//    $filename = lang('invoice') . '_' . str_replace(['\\', '/'], '_', $invoice->invoice_number);
//
//    $html = $CI->load->view('invoice_templates/pdf/' . $invoiceTemplate, $data, true);
//
//    if ($invoiceTemplate) {
//        $footerFile = __DIR__ . '/../../assets/invoices/footer.html';
//        $tmpFooterFile = __DIR__ . '/../../assets/invoices/' . uniqid('footer-') . '.html';
//        $css = strtr($invoiceTemplate, ['invoice' => 'invoice-']);
//        $footer = strtr(
//            file_get_contents($footerFile),
//            [
//                '{{ css }}'      => $css,
//                '{{ company }}'  => $invoice->company_name,
//                '{{ reg_code }}' => $invoice->company_code,
//                '{{ vat }}'      => $invoice->company_vatregnumber,
//                '{{ email }}'    => $invoice->user_email,
//                '{{ bank }}'     => $invoice->company_bank_bic,
//                '{{ swift }}'    => $invoice->company_bank_bic,
//                '{{ iban }}'     => $invoice->company_iban,
//                '{{ address }}'  => $invoice->client_address_1,
//                '{{ url }}'      => $invoice->company_url
//            ]
//        );
//
//        file_put_contents($tmpFooterFile, $footer);
//
//        $pdf = new Pdf($html);
//        $pdf->setOptions([
//            'no-outline',
//            'margin-left'    => 0,
//            'margin-right'   => 0,
//            'footer-spacing' => 4,
//            'footer-html'    => $tmpFooterFile,
//        ]);
//
//        $ppath = '/../../uploads/zip_exp/' . $invoice->company_id . '/';
//        if (!(is_dir($ppath) OR is_link($ppath))) {
//            mkdir($ppath, 0777);
//            chmod($ppath, 0777);
//        }
//
//        if (!$pdf->saveAs($ppath . $filename)) {
////            echo $pdf->getError();
////            return;
//        };
//
//        unlink($tmpFooterFile);
//
//
//        return [
//            'path'     => $savePath,
//            'filename' => $filename . '.pdf'
//        ];
//    }
//}

function generate_invoice_pdf($invoiceId, $stream = false, $invoiceTemplate = null, $isGuest = null)
{
    $CI = &get_instance();

    $CI->load->model('invoices/mdl_invoices');
    $CI->load->model('invoices/mdl_items');
    $CI->load->model('invoices/mdl_invoice_tax_rates');
    $CI->load->model('mdl_payment_methods');
    $CI->load->library('encrypt');
    $CI->load->library('towords');
    $CI->load->helper('qr');

    $invoice = $CI->mdl_invoices->get_by_id($invoiceId);

    if (!$invoiceTemplate) {
        $invoiceTemplate = 'default';
    }

    $paymentMethod = $CI->mdl_payment_methods
        ->where('payment_method_id', $invoice->payment_method)
        ->get()
        ->row();

    if ($invoice->payment_method == 0) {
        $paymentMethod = null;
    }

    $qr_code_file_path = generate_qr_code($invoice);

    if ($invoice->is_received == 1) {
        $invoice = reverse_details_rec_invoices($invoice);
    }

    $items = $CI->mdl_items->where('invoice_id', $invoiceId)->get()->result();
    $itemsDiscount = false;
    foreach ($items as $i) {
        if ($i->item_discount_percent > 0) {
            $itemsDiscount = true;
            break;
        }
    }

    $data = [
        'qr_code_file_path' => $qr_code_file_path,
        'invoice'           => $invoice,
        'invoice_tax_rates' => $CI->mdl_invoice_tax_rates->where('invoice_id', $invoiceId)->get()->result(),
        'items'             => $items,
        'itemsDiscount'     => $itemsDiscount,
        'payment_method'    => $paymentMethod,
        'output_type'       => 'pdf'
    ];

    $filename = lang('invoice') . '_' . str_replace(['\\', '/'], '_', $invoice->invoice_number);

    $html = $CI->load->view('invoice_templates/pdf/' . $invoiceTemplate, $data, true);

    if ($invoiceTemplate) {
        $footerFile = __DIR__ . '/../../assets/invoices/footer.html';
        $tmpFooterFile = __DIR__ . '/../../assets/invoices/' . uniqid('footer-') . '.html';
        $css = strtr($invoiceTemplate, ['invoice' => 'invoice-']);
        $footer = strtr(
            file_get_contents($footerFile),
            [
                '{{ css }}'      => $css,
                '{{ company }}'  => $invoice->company_name,
                '{{ reg_code }}' => $invoice->company_code,
                '{{ vat }}'      => $invoice->company_vatregnumber,
                '{{ email }}'    => $invoice->user_email,
                '{{ bank }}'     => $invoice->company_bank_bic,
                '{{ swift }}'    => $invoice->company_bank_bic,
                '{{ iban }}'     => $invoice->company_iban,
                '{{ address }}'  => $invoice->client_address_1,
                '{{ url }}'      => $invoice->company_url
            ]
        );

        file_put_contents($tmpFooterFile, $footer);

        $pdf = new Pdf($html);
        $pdf->setOptions([
            'no-outline',
            'margin-left'    => 0,
            'margin-right'   => 0,
            'footer-spacing' => 4,
            'footer-html'    => $tmpFooterFile,
        ]);

        if (!$pdf->saveAs('/usr/share/nginx/html/uploads/pdf/invoices/' . $filename)) {
            echo $pdf->getError();

            return;
        };

        unlink($tmpFooterFile);

        if ($stream == true) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename=' . $filename . '.pdf');
            echo $pdf->toString();
        } else {
            $savePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('invoice-');
            $pdf->saveAs($savePath);

            return [
                'path'     => $savePath,
                'filename' => $filename . '.pdf'
            ];
        }
    }

    $CI->load->helper('mpdf');

    return pdf_create_custom_template(
        $html, $filename, $stream,
        $invoice->invoice_password,
        1, $isGuest, $invoiceId
    );
}

function generate_invoice_loop($invoiceId, $stream = false, $invoiceTemplate = null, $isGuest = null)
{
    $CI = &get_instance();

    $CI->load->model('invoices/mdl_invoices');
    $CI->load->model('invoices/mdl_items');
    $CI->load->model('invoices/mdl_invoice_tax_rates');
    $CI->load->model('mdl_payment_methods');
    //$CI->load->library('encrypt');
    //$CI->load->library('towords');
    $CI->load->helper('qr');

    $invoice = $CI->mdl_invoices->get_by_id($invoiceId);

    if (!$invoiceTemplate) {
        $invoiceTemplate = 'default';
    }

    $paymentMethod = $CI->mdl_payment_methods
        ->where('payment_method_id', $invoice->payment_method)
        ->get()
        ->row();

    if ($invoice->payment_method == 0) {
        $paymentMethod = null;
    }

    $qr_code_file_path = generate_qr_code($invoice);

    if ($invoice->is_received == 1) {
        $invoice = reverse_details_rec_invoices($invoice);
    }

    $items = $CI->mdl_items->where('invoice_id', $invoiceId)->get()->result();
    $itemsDiscount = false;
    foreach ($items as $i) {
        if ($i->item_discount_percent > 0) {
            $itemsDiscount = true;
            break;
        }
    }

    $data = [
        'qr_code_file_path' => $qr_code_file_path,
        'invoice'           => $invoice,
        'invoice_tax_rates' => $CI->mdl_invoice_tax_rates->where('invoice_id', $invoiceId)->get()->result(),
        'items'             => $items,
        'itemsDiscount'     => $itemsDiscount,
        'payment_method'    => $paymentMethod,
        'output_type'       => 'pdf'
    ];

    $filename = $invoiceId . "__" . lang('invoice') . '_' . str_replace(['\\', '/'], '_', $invoice->invoice_number);

    $html = $CI->load->view('invoice_templates/pdf/' . $invoiceTemplate, $data, true);

    if ($invoiceTemplate) {
        $footerFile = __DIR__ . '/../../assets/invoices/footer.html';
        $tmpFooterFile = __DIR__ . '/../../assets/invoices/' . uniqid('footer-') . '.html';
        $css = strtr($invoiceTemplate, ['invoice' => 'invoice-']);
        $footer = strtr(
            file_get_contents($footerFile),
            [
                '{{ css }}'      => $css,
                '{{ company }}'  => $invoice->company_name,
                '{{ reg_code }}' => $invoice->company_code,
                '{{ vat }}'      => $invoice->company_vatregnumber,
                '{{ email }}'    => $invoice->user_email,
                '{{ bank }}'     => $invoice->company_bank_bic,
                '{{ swift }}'    => $invoice->company_bank_bic,
                '{{ iban }}'     => $invoice->company_iban,
                '{{ address }}'  => $invoice->client_address_1,
                '{{ url }}'      => $invoice->company_url
            ]
        );

        file_put_contents($tmpFooterFile, $footer);

        $pdf = new Pdf($html);
        $pdf->setOptions([
            'no-outline',
            'margin-left'    => 0,
            'margin-right'   => 0,
            'footer-spacing' => 4,
            'footer-html'    => $tmpFooterFile,
        ]);

        if (!(is_dir('/usr/share/nginx/html/uploads/zip_exp/') OR is_link('/usr/share/nginx/html/uploads/zip_exp/'))) {
            mkdir('/usr/share/nginx/html/uploads/zip_exp/', 0777);
            chmod('/usr/share/nginx/html/uploads/zip_exp/', 0777);
        }

        if (!(is_dir('/usr/share/nginx/html/uploads/zip_exp/' . $invoice->company_id . '/') OR is_link('/usr/share/nginx/html/uploads/zip_exp/' . $invoice->company_id . '/'))) {
            mkdir('/usr/share/nginx/html/uploads/zip_exp/' . $invoice->company_id . '/', 0777);
            chmod('/usr/share/nginx/html/uploads/zip_exp/' . $invoice->company_id . '/', 0777);
        }

        if (!$pdf->saveAs('/usr/share/nginx/html/uploads/zip_exp/' . $invoice->company_id . '/' . $filename . '.pdf' )) {
            echo  "nothing";
        } else {
            return [
                'zip'     => '/usr/share/nginx/html/uploads/zip_exp/' ,
                'dtl' => array(
                    'path'     => '/usr/share/nginx/html/uploads/zip_exp/' . $invoice->company_id . '/' ,
                    'filename' => $filename . '.pdf'
                )
            ];
        }

        unlink($tmpFooterFile);

//        $savePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('invoice-');
//        $pdf->saveAs($savePath);
//        return [
//            'path'     => $savePath,
//            'filename' => $filename . '.pdf'
//        ];
    }
}

function generate_quote_pdf($quoteId, $stream = true, $quoteTemplate = null)
{
    $CI = &get_instance();

    $CI->load->model('quotes/mdl_quotes');
    $CI->load->model('quotes/mdl_quote_items');
    $CI->load->model('quotes/mdl_quote_tax_rates');
    $CI->load->helper('qr');
    $CI->load->helper('quotes_helper');

    $quote = get_quote_details($quoteId);

    if (!$quoteTemplate) {
        $quoteTemplate = 'invoice1';
    }

    $qr_code_file_path = generate_qr_code($quote);
    $items = $CI->mdl_quote_items->where('quote_id', $quoteId)->get()->result();
    $itemsDiscount = false;
    foreach ($items as $i) {
        if ($i->item_discount_percent > 0) {
            $itemsDiscount = true;
            break;
        }
    }

    $data = [
        'qr_code_file_path' => $qr_code_file_path,
        'quote'             => $quote,
        'quote_tax_rates'   => $CI->mdl_quote_tax_rates->where('quote_id', $quoteId)->get()->result(),
        'items'             => $items,
        'itemsDiscount'     => $itemsDiscount,
        'output_type'       => 'pdf'
    ];

    $filename = lang('quote') . '_' . str_replace(['\\', '/'], '_', $quote['quote']['quote_number']);
    $html = $CI->load->view('quote_templates/pdf/' . $quoteTemplate, $data, true);

    if ($quoteTemplate) {
        $footerFile = __DIR__ . '/../../assets/invoices/footer.html';
        $tmpFooterFile = __DIR__ . '/../../assets/invoices/' . uniqid('footer-') . '.html';
        $css = strtr($quoteTemplate, ['invoice' => 'invoice-']);
        $footer = strtr(
            file_get_contents($footerFile),
            [
                '{{ css }}'      => $css,
                '{{ company }}'  => $quote->company_name,
                '{{ reg_code }}' => $quote->company_code,
                '{{ vat }}'      => $quote->company_vatregnumber,
                '{{ email }}'    => $quote->user_email,
                '{{ bank }}'     => $quote->company_bank_bic,
                '{{ iban }}'     => $quote->client_iban,
                '{{ url }}'      => $quote->company_url
            ]
        );

        file_put_contents($tmpFooterFile, $footer);

        $pdf = new Pdf($html);
        $pdf->setOptions([
            'no-outline',
            'margin-left'    => 0,
            'margin-right'   => 0,
            'footer-spacing' => 4,
            'footer-html'    => $tmpFooterFile,
        ]);

        if (!$pdf->saveAs('/usr/share/nginx/html/uploads/pdf/quotes/' . $filename)) {
            echo $pdf->getError();

            return;
        };

        unlink($tmpFooterFile);

        if ($stream == true) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename=' . $filename . '.pdf');
            echo $pdf->toString();
        } else {
            $savePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('quote-');
            $pdf->saveAs($savePath);

            return [
                'path'     => $savePath,
                'filename' => $filename . '.pdf'
            ];
        }
    }

    $CI->load->helper('mpdf');

    return pdf_create($html, $filename, $stream, $quote->quote_password);
}
