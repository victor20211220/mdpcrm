<?php

use mikehaertl\wkhtmlto\Pdf;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function generate_invoice_pdf($invoice_id, $stream = true, $invoice_template = null, $isGuest = null)
{
    $CI = &get_instance();

    $CI->load->model('invoices/Mdl_invoices');
    $CI->load->model('invoices/Mdl_items');
    $CI->load->model('invoices/Mdl_invoice_tax_rates');
    $CI->load->model('Mdl_payment_methods');
    $CI->load->library('encrypt');
    $CI->load->library('towords');
    $CI->load->helper('qr');

    $invoice = $CI->Mdl_invoices->getByPk($invoice_id);

    if (!$invoice_template) {
        $CI->load->helper('template');
        $invoice_template = 'invoice1';
    }

    $get_invoice = file_get_contents(__DIR__ . '/../../assets/invoices/' . $invoice_template . '.html');

    $get_invoice_row = $CI->db->get_where('ip_invoices', ['invoice_id' => $invoice_id])->row_array(); // get invoice row

    $company_logo = $CI->db->get_where('ip_settings', [
        'setting_key' => 'invoice_logo',
        'company_id'  => $get_invoice_row['company_id']
    ])->row('setting_value'); // get logo
    $from_row = $CI->db->get_where('ip_companies',
        ['company_id' => $get_invoice_row['company_id']]
    )->row_array(); // get company row
    $user_email = $CI->db->get_where('ip_users',
        ['user_id' => $get_invoice_row['user_id']]
    )->row('user_email'); //get user email

    $invoice_totals = $CI->db->get_where('ip_invoice_amounts',
        ['invoice_id' => $invoice_id]
    )->row_array(); // get invoice row

    $to_row = $CI->db->get_where('ip_clients',
        ['client_id' => $get_invoice_row['client_id']]
    )->row_array(); // get client row

    $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    $in_words = $f->format($invoice_totals['invoice_total']);

    $qr_code_file_path = str_replace('/usr/share/nginc/html/application/helpers/../../', '/usr/share/nginc/html/',
        generate_qr_code($invoice)); //generate qr code
    //$qr_code = site_url($qr_code_file_path);

    /* get invoice items */
    $item_amount_row = [];
    $inv = [];
    $tax_rate = '';

    foreach ($CI->db->get_where('ip_invoice_items', ['invoice_id' => $invoice_id])->result_array() as $inv) {
        $item_amount_row = $CI->db->get_where('ip_invoice_item_amounts',
            ['item_amount_id' => $inv['item_id']])->row_array(); //get amounts
        $tax_rate = $CI->db->get_where('ip_tax_rates', [
            'tax_rate_id' => $CI->db->get_where('ip_products',
                ['product_id' => $inv['item_product_id']])->row('tax_rate_id')
        ])->row('tax_rate_percent'); //get tax rate percent
        $items_array[] = '<tr>
                    <td>
                      ' . $CI->db->get_where('ip_products',
                ['product_id' => $inv['item_product_id']])->row('product_name') . '
                      <div class="description">' . $CI->db->get_where('ip_products',
                ['product_id' => $inv['item_product_id']])->row('product_description') . '</div>
                    </td>
                    <td>' . $inv['item_quantity'] . '</td>
                    <td>' . $item_amount_row['item_total'] . '</td>
                    <td>' . $item_amount_row['item_discount'] . '</td>
                    <td>' . $tax_rate . '</td>
                    <td>' . $item_amount_row['item_tax_total'] . '</td>
                    <td>' . $item_amount_row['item_subtotal'] . '</td>
                  </tr>';
    }
    $items = implode(' ', $items_array);


    $invoice_final = str_replace(
        [
            '{company_logo}',
            '{date}',
            '{number}',
            '{from_name}',
            '{from_address}',
            '{from_code}',
            '{from_vat}',
            '{from_bank}',
            '{to_name}',
            '{to_address}',
            '{to_code}',
            '{to_vat}',
            '{to_bank}',
            '{total_ex_vat}',
            '{total_vat}',
            '{total}',
            '{amount_in_words}',
            '{comments}',
            '{from_email}',
            '{from_bank_account}',
            '{from_iban}',
            '{items}',
            '{css}',
            '{qr_code}'
        ],
        [
            '/usr/share/nginx/html/uploads/' . $company_logo,
            $get_invoice_row['invoice_date_created'],
            $get_invoice_row['invoice_number'],
            $from_row['company_name'],
            $from_row['company_address'],
            $from_row['company_code'],
            $from_row['company_vat'],
            $from_row['company_bank_bic'],
            $to_row['client_name'],
            $to_row['client_address_1'] . ' ' . $to_row['client_address_2'] . ' ' . $to_row['client_city'] . ' ' . $to_row['client_state'] . ' ' . $to_row['client_country'],
            $to_row['client_tax_code'],
            $to_row['client_vat_id'],
            $to_row['client_swift'],
            $invoice_totals['invoice_item_subtotal'],
            $invoice_totals['invoice_total'] - $invoice_totals['invoice_item_subtotal'],
            $invoice_totals['invoice_total'],
            $in_words,
            $get_invoice_row['invoice_terms'],
            $user_email,
            $from_row['company_bank_bic'],
            $from_row['company_iban'],
            $items,
            '/usr/share/nginx/html/assets/invoices/assets/css/' . $invoice_template . '.css',
            $qr_code_file_path
        ],
        $get_invoice);


    file_put_contents("invoice.html", $invoice_final);

    //$pdf_file = file_get_contents("temp-invoice.html");

    $pdf = new Pdf("invoice.html");
    if (!$pdf->saveAs('invoice.pdf')) {
        echo $pdf->getError();
    } else {
        $pdf->send('invoice.pdf');
    }

    //die;


    //echo $invoice_final; die;
    $payment_method = $CI->Mdl_payment_methods->where('payment_method_id', $invoice->payment_method)->get()->row();

    if ($invoice->payment_method == 0) {
        $payment_method = null;
    }


    if ($invoice->is_received == 1) {
        $invoice = reverse_details_rec_invoices($invoice);
    }

    $data = [
        'qr_code_file_path' => $qr_code_file_path,
        'invoice'           => $invoice,
        'invoice_tax_rates' => $CI->Mdl_invoice_tax_rates->where('invoice_id', $invoice_id)->get()->result(),
        'items'             => $CI->Mdl_items->where('invoice_id', $invoice_id)->get()->result(),
        'payment_method'    => $payment_method,
        'output_type'       => 'pdf'
    ];

    $html = $CI->load->view('invoice_templates/pdf/' . $invoice_template, $data, true);
    if (in_array($invoice_template, ['invoice1', 'invoice4', 'invoice6', 'invoice9', 'invoice10'])) {
        $footerFile = __DIR__ . '/../../assets/invoices/footer.html';
        $tmpFooterFile = __DIR__ . '/../../assets/invoices/' . uniqid('footer-') . '.html';
        $css = strtr($invoice_template, ['invoice' => 'invoice-']);
        $footer = strtr(
            file_get_contents($footerFile),
            [
                '{{ css }}'      => $css,
                '{{ company }}'  => $invoice->company_name,
                '{{ reg_code }}' => $invoice->company_code,
                '{{ vat }}'      => $invoice->company_vatregnumber,
                '{{ email }}'    => $invoice->user_email,
                '{{ bank }}'     => $invoice->company_bank_bic,
                '{{ iban }}'     => $invoice->client_iban,
            ]
        );
        file_put_contents($tmpFooterFile, $footer);

        // Generate pdf with wkhtmltopdf utility
        $pdf = new Pdf($get_invoice);
        $pdf->setOptions([
            'no-outline',
            'margin-left'    => 0,
            'margin-right'   => 0,
            'footer-spacing' => 4,
            'footer-html'    => $tmpFooterFile,
        ]);
        $filename = lang('invoice') . '_' . str_replace(['\\', '/'], '_', $invoice->invoice_number);
        if (!$pdf->saveAs($filename)) {
            echo $pdf->getError();

            return;
        };
        unlink($tmpFooterFile);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename=' . lang('invoice') . '-' . str_replace(['\\', '/'], '-',
                $invoice->invoice_number) . '.pdf');
        echo $pdf->toString();

        return;
    }

    $CI->load->helper('mpdf');

    return pdf_create_custom_template($html,
        lang('invoice') . '_' . str_replace(['\\', '/'], '_', $invoice->invoice_number), $stream,
        $invoice->invoice_password, 1, $isGuest, $invoice_id);
}

function generate_quote_pdf($quote_id, $stream = true, $quote_template = null)
{
    $CI = &get_instance();

    $CI->load->model('quotes/Mdl_quotes');
    $CI->load->model('quotes/Mdl_quote_items');
    $CI->load->model('quotes/Mdl_quote_tax_rates');
    $CI->load->helper('qr');

    $quote = $CI->Mdl_quotes->getByPk($quote_id);

    if (!$quote_template) {
        $quote_template = $CI->Mdl_settings->setting('pdf_quote_template');
    }

    $qr_code_file_path = generate_qr_code($quote);

    $data = [
        'qr_code_file_path' => $qr_code_file_path,
        'quote'             => $quote,
        'quote_tax_rates'   => $CI->Mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result(),
        'items'             => $CI->Mdl_quote_items->where('quote_id', $quote_id)->get()->result(),
        'output_type'       => 'pdf'
    ];

    $html = $CI->load->view('quote_templates/pdf/' . $quote_template, $data, true);
    if (in_array($quote_template, ['invoice1', 'invoice4', 'invoice6', 'invoice9', 'invoice10'])) {
        $footerFile = __DIR__ . '/../../assets/invoices/footer.html';
        $tmpFooterFile = __DIR__ . '/../../assets/invoices/' . uniqid('footer-') . '.html';
        $css = strtr($quote_template, ['invoice' => 'invoice-']);
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
            ]
        );
        file_put_contents($tmpFooterFile, $footer);

        // Generate pdf with wkhtmltopdf utility
        $pdf = new Pdf($html);
        $pdf->setOptions([
            'no-outline',
            'margin-left'    => 0,
            'margin-right'   => 0,
            'footer-spacing' => 4,
            'footer-html'    => $tmpFooterFile,
        ]);
        $filename = lang('invoice') . '_' . str_replace(['\\', '/'], '_', $quote->invoice_number);
        if (!$pdf->saveAs($filename)) {
            echo $pdf->getError();

            return;
        };
        unlink($tmpFooterFile);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename=' . lang('quote') . '-' . str_replace(['\\', '/'], '-',
                $quote->invoice_number) . '.pdf');
        echo $pdf->toString();

        return;
    }

    $CI->load->helper('mpdf');

    return pdf_create($html, lang('quote') . '_' . str_replace(['\\', '/'], '_', $quote->quote_number), $stream,
        $quote->quote_password);
}
