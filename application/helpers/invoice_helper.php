<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function convert_number($number)
{
    if (($number < 0) || ($number > 999999999)) {
        throw new Exception("Number is out of range");
    }

    $Gn = floor($number / 1000000);  /* Millions (giga) */
    $number -= $Gn * 1000000;
    $kn = floor($number / 1000);     /* Thousands (kilo) */
    $number -= $kn * 1000;
    $Hn = floor($number / 100);      /* Hundreds (hecto) */
    $number -= $Hn * 100;
    $Dn = floor($number / 10);       /* Tens (deca) */
    $n = $number % 10;               /* Ones */

    $res = "";

    if ($Gn) {
        $res .= convert_number($Gn) . " Million";
    }

    if ($kn) {
        $res .= (empty($res) ? "" : " ") .
            convert_number($kn) . " Thousand";
    }

    if ($Hn) {
        $res .= (empty($res) ? "" : " ") .
            convert_number($Hn) . " Hundred";
    }

    $ones = [
        "",
        "One",
        "Two",
        "Three",
        "Four",
        "Five",
        "Six",
        "Seven",
        "Eight",
        "Nine",
        "Ten",
        "Eleven",
        "Twelve",
        "Thirteen",
        "Fourteen",
        "Fifteen",
        "Sixteen",
        "Seventeen",
        "Eightteen",
        "Nineteen"
    ];
    $tens = [
        "",
        "",
        "Twenty",
        "Thirty",
        "Fourty",
        "Fifty",
        "Sixty",
        "Seventy",
        "Eigthy",
        "Ninety"
    ];

    if ($Dn || $n) {
        if (!empty($res)) {
            $res .= " and ";
        }

        if ($Dn < 2) {
            $res .= $ones[$Dn * 10 + $n];
        } else {
            $res .= $tens[$Dn];

            if ($n) {
                $res .= "-" . $ones[$n];
            }
        }
    }

    if (empty($res)) {
        $res = "zero";
    }

    return $res;
}


function reverse_details_rec_invoices($invoice)
{


    $CI = &get_instance();

    $CI->load->model('Mdl_companies');


    $company = $CI->Mdl_companies->get_array_by_id($CI->session->userdata('company_id'));


    //this case, when the invoice was created by another company in my sistem

    $invoice->company_name = $invoice->client_name;
    $invoice->client_name = $company['company_name'];

    $aux = $invoice->company_vatregnumber;
    $invoice->company_vatregnumber = $invoice->client_vat_id;
    $invoice->client_vat_id = $aux;

    $aux = $invoice->company_address;
    $invoice->company_address = $invoice->client_address_1;
    $invoice->client_address_1 = $aux;

    $aux = $invoice->company_country;
    $invoice->company_country = $invoice->client_country;
    $invoice->client_country = $aux;

    $aux = $invoice->user_phone;
    $invoice->user_phone = $invoice->client_phone;
    $invoice->client_phone = $aux;

    $aux = $invoice->user_fax;
    $invoice->user_fax = $invoice->client_fax;
    $invoice->client_fax = $aux;

    $aux = $invoice->user_email;
    $invoice->user_email = $invoice->client_email;
    $invoice->client_email = $aux;

    $aux = $invoice->company_code;
    $invoice->company_code = $invoice->client_reg_number;
    $invoice->client_reg_number = $aux;

    $invoice->client_tax_code = '';

    $aux = $invoice->user_city;
    $invoice->user_city = $invoice->client_city;
    $invoice->client_city = $aux;

    $aux = $invoice->user_zip;
    $invoice->user_zip = $invoice->client_zip;
    $invoice->client_zip = $aux;

    $aux = $invoice->user_state;
    $invoice->user_state = $invoice->client_state;
    $invoice->client_state = $aux;

    $aux = $invoice->company_iban;
    $invoice->company_iban = $invoice->client_iban;
    $invoice->client_iban = $aux;

    return $invoice;
}


function invoice_qr_img($path)
{
    if (isset($path) && $path != '') {
        //echo $path;
        return '<img src="' . $path . '" class="qrimg" style="width=80px !important;height:80px !important;">';
    }

    return '';
}

function getQrCode($path)
{
    return '<img src="' . $path . '" class="qrimg" style="width=80px !important;height:80px !important;">';
}

function invoice_logo()
{
    $CI = &get_instance();

    if ($CI->Mdl_settings->setting('invoice_logo')) {
        //echo base_url() . 'uploads/' . $CI->Mdl_settings->setting('invoice_logo');
        //return '<img src="' . base_url() . 'uploads/' . $CI->Mdl_settings->setting('invoice_logo') . '">';
        return '<img src="' . site_url('uploads/' . $CI->Mdl_settings->setting('invoice_logo')) . '" width="300">';
    }

    return '';
}


function invoice_logo_pdf()
{
    $CI = &get_instance();

    if ($CI->Mdl_settings->setting('invoice_logo')) {
        //echo getcwd() . '/uploads/' . $CI->Mdl_settings->setting('invoice_logo');
        return '<img src="' . getcwd() . '/uploads/' . $CI->Mdl_settings->setting('invoice_logo') .
            '" id="invoice-logo" width="200px" style="width:200px !important;max-height:80px !important">';
    }

    return '';
}

function convert_number_to_words($number)
{
    $hyphen = '-';
    $conjunction = ' and ';
    $separator = ', ';
    $negative = 'negative ';
    $decimal = ' euros and ';
    $dictionary = [
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    ];

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MIN . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );

        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens = ((int)($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int)($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = [];
        foreach (str_split((string)$fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
    global $CI;
    $currency_symbol = $CI->Mdl_settings->setting('currency_symbol');

    return $string;
}
