<?php



if (!defined('BASEPATH'))

    exit('No direct script access allowed');







function phpmail_send($from, $to, $subject, $message, $attachment_path = NULL, $cc = NULL, $bcc = NULL, $more_attachments = NULL)

{

    require_once(APPPATH . 'modules/mailer/helpers/phpmailer/class.phpmailer.php');



    $CI = &get_instance();

    $CI->load->library('encrypt');



    // Create the basic mailer object

    $mail = new PHPMailer();

    $mail->CharSet = 'UTF-8';

    $mail->IsHtml();



    switch ($CI->Mdl_settings->setting('email_send_method')) {

        case 'smtp':

            $mail->IsSMTP();



            // Set the basic properties

            $mail->Host = $CI->Mdl_settings->setting('smtp_server_address');

            $mail->Port = $CI->Mdl_settings->setting('smtp_port');



            // Is SMTP authentication required?

            if ($CI->Mdl_settings->setting('smtp_authentication')) {

                $mail->SMTPAuth = TRUE;

                $mail->Username = $CI->Mdl_settings->setting('smtp_username');

                $mail->Password = $CI->encrypt->decode($CI->Mdl_settings->setting('smtp_password'));

            }



            // Is a security method required?

            if ($CI->Mdl_settings->setting('smtp_security')) {

                $mail->SMTPSecure = $CI->Mdl_settings->setting('smtp_security');

            }



            break;

        case 'sendmail':

            //$mail->IsMail();

            break;

        case 'phpmail':

        case 'default':

            $mail->IsMail();

            break;

    }



    $mail->Subject = $subject;

    $mail->Body = $message;

    $mail->SMTPDebug = 0;



    if (is_array($from)) {

        // This array should be address, name

        $mail->SetFrom($from[0], $from[1]);

    } else {

        // This is just an address

        $mail->SetFrom($from);

    }



    // Allow multiple recipients delimited by comma or semicolon

    $to = (strpos($to, ',')) ? explode(',', $to) : explode(';', $to);



    // Add the addresses

    foreach ($to as $address) {

        $mail->AddAddress($address);

    }



    if ($cc) {

        // Allow multiple CC's delimited by comma or semicolon

        $cc = (strpos($cc, ',')) ? explode(',', $cc) : explode(';', $cc);



        // Add the CC's

        foreach ($cc as $address) {

            $mail->AddCC($address);

        }

    }



    if ($bcc) {



        // Allow multiple BCC's delimited by comma or semicolon

        $bcc = (strpos($bcc, ',')) ? explode(',', $bcc) : explode(';', $bcc);

        // Add the BCC's

        foreach ($bcc as $address) {

            $mail->AddBCC($address);

        }



    }



    if ($CI->Mdl_settings->setting('bcc_mails_to_admin') == 1) {

        // Get email address of admin account and push it to the array

        $CI->load->model('users/Mdl_users');

        $CI->db->where('user_id', 1);

        $admin = $CI->db->get('ip_users')->row();

        $mail->AddBCC($admin->user_email);

    }



    // Add the attachment if supplied

    if ($attachment_path && $CI->Mdl_settings->setting('email_pdf_attachment')) {

        $mail->AddAttachment($attachment_path);

    }

    // Add the other attachments if supplied

    if ($more_attachments) {



        foreach ($more_attachments as $paths) {

            $mail->AddAttachment($paths['path'], $paths['filename']);

        }

    }





    // And away it goes...

    if ($mail->Send()) {

        $CI->session->set_flashdata('alert_success', 'The email has been sent');

        return TRUE;

    } else {

        // Or not...

        $CI->session->set_flashdata('alert_error', $mail->ErrorInfo);

        return FALSE;

    }

}
