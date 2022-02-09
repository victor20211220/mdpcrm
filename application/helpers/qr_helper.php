<?php

function generate_qr_code($invoice)
{
    require_once APPPATH . 'helpers/phpqrcode/qrlib.php';

    $filePath = __DIR__ . '/../../uploads/qr_code/' . date('Y-m-d') . '_' . $invoice->invoice_url_key . '_' . $invoice->invoice_id . '.png';
    $link = site_url('guest/view/invoice/' . $invoice->invoice_url_key);
    QRcode::png($link, $filePath);

    return $filePath;
}
