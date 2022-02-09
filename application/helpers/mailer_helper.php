<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function mailer_configured()
{
    $CI = &get_instance();

    return (
        ($CI->Mdl_settings->setting('email_send_method') == 'phpmail') OR
        ($CI->Mdl_settings->setting('email_send_method') == 'sendmail') OR
        (
            ($CI->Mdl_settings->setting('email_send_method') == 'smtp') AND
            ($CI->Mdl_settings->setting('smtp_server_address'))
        )
    );
}

function test_smtp()
{
    require_once(APPPATH . 'modules/mailer/helpers/phpmailer/class.phpmailer.php');
    require_once(APPPATH . 'modules/mailer/helpers/phpmailer/class.smtp.php');

    $CI = &get_instance();
    $CI->load->library('encrypt');

    // Create the basic mailer object

    $mail = new PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->IsHtml();

    switch ($CI->Mdl_settings->setting('email_send_method')) {
        case 'smtp':
            $mail->IsSMTP();
            $mail->SMTPDebug = 0;
            $mail->Host = $CI->Mdl_settings->setting('smtp_server_address');
            $mail->Port = $CI->Mdl_settings->setting('smtp_port');

            if ($CI->Mdl_settings->setting('smtp_authentication')) {
                $mail->SMTPAuth = true;
                $mail->Username = $CI->Mdl_settings->setting('smtp_username');
                $mail->Password = $CI->encrypt->decode($CI->Mdl_settings->setting('smtp_password'));
            }

            if ($CI->Mdl_settings->setting('smtp_security')) {
                $mail->SMTPSecure = $CI->Mdl_settings->setting('smtp_security');
            }

            break;

        case 'sendmail':
            $mail->IsMail();
            break;

        case 'phpmail':

        case 'default':
            $mail->IsMail();
            break;
    }

    try {
        if (!$mail->SmtpConnect()) {
            $CI->session->set_flashdata('alert_error', "SMTP error:" . $mail->ErrorInfo);

            return false;
        }

        $CI->session->set_flashdata('alert_success', 'SMTP OK!');

        return true;
    } catch (phpmailerException $e) {
        $CI->session->set_flashdata('alert_error', "SMTP error:" . $e->getMessage());

        return false;
    }

    return true;
}

/**
 * @param $invoiceId
 * @param $invoiceTemplate
 * @param $from
 * @param $to
 * @param $subject
 * @param $body
 * @param null $cc
 * @param null $bcc
 * @param null $attachments
 * @return bool
 */
function email_invoice(
    $invoiceId,
    $invoiceTemplate,
    $from,
    $to,
    $subject,
    $body,
    $cc = null,
    $bcc = null,
    $attachments = null
) {
    $CI = &get_instance();

    $CI->load->helper('mailer/phpmailer');
    $CI->load->helper('template');
    $CI->load->helper('invoice');
    $CI->load->helper('pdf');

    $invoice = generate_invoice_pdf($invoiceId, false, $invoiceTemplate);
    $attachments[] = $invoice;

    $invoiceData = $CI->Mdl_invoices
        ->where('ip_invoices.invoice_id', $invoiceId)
        ->get()
        ->row();

    $message = parse_template($invoiceData, $body);
    $subject = parse_template($invoiceData, $subject);
    $cc = parse_template($invoiceData, $cc);
    $bcc = parse_template($invoiceData, $bcc);
    $from = [parse_template($invoiceData, $from[0]), parse_template($invoiceData, $from[1])];

    return phpmail_send($from, $to, $subject, $message, $invoice, $cc, $bcc, $attachments);
}

/**
 * Email quote
 * @param $quoteId
 * @param $quoteTemplate
 * @param $from
 * @param $to
 * @param $subject
 * @param $body
 * @param null $cc
 * @param null $bcc
 * @param null $attachments
 * @return bool
 */
function email_quote(
    $quoteId,
    $quoteTemplate,
    $from,
    $to,
    $subject,
    $body,
    $cc = null,
    $bcc = null,
    $attachments = null
) {
    $CI = &get_instance();

    $CI->load->helper('mailer/phpmailer');
    $CI->load->helper('template');
    $CI->load->helper('pdf');

    $quote = generate_quote_pdf($quoteId, false, $quoteTemplate);
    $attachments[] = $quote;

    $quoteData = $CI->Mdl_quotes->where('ip_quotes.quote_id', $quoteId)->get()->row();
    $message = parse_template($quoteData, $body);
    $subject = parse_template($quoteData, $subject);
    $cc = parse_template($quoteData, $cc);
    $bcc = parse_template($quoteData, $bcc);
    $from = [parse_template($quoteData, $from[0]), parse_template($quoteData, $from[1])];

    return phpmail_send($from, $to, $subject, $message, $quote, $cc, $bcc, $attachments);
}


/**
 * Email quote status
 * @param $quoteId
 * @param $status
 * @return bool
 */
function email_quote_status($quoteId, $status)
{
    ini_set("display_errors", "on");
    error_reporting(E_ALL);

    if (!mailer_configured()) {
        return false;
    }

    $CI = &get_instance();
    $CI->load->helper('mailer/phpmailer');

    $quote = $CI->Mdl_quotes->where('ip_quotes.quote_id', $quoteId)->get()->row();
    $baseUrl = base_url('/quotes/view/' . $quoteId);

    $userEmail = $quote->user_email;
    $subject = sprintf(lang('quote_status_email_subject'),
        $quote->client_name,
        strtolower(lang($status)),
        $quote->quote_number
    );

    $body = sprintf(nl2br(lang('quote_status_email_body')),
        $quote->client_name,
        strtolower(lang($status)),
        $quote->quote_number,
        '<a href="' . $baseUrl . '">' . $baseUrl . '</a>'
    );

    return phpmail_send($userEmail, $userEmail, $subject, $body);
}
