<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function pdf_create($html, $filename, $stream = TRUE, $password = NULL, $isInvoice = NULL, $isGuest = NULL)
{
    require_once (APPPATH . 'helpers/mpdf/mpdf.php');
    $mpdf = new mPDF('', '', '', '', 0, 0, 7, 0);
    $mpdf->useAdobeCJK = true;
    $mpdf->SetAutoFont();
    $mpdf->SetProtection(array(
        'copy',
        'print'
    ), $password, $password);

    if (!(is_dir('./uploads/archive/') OR is_link('./uploads/archive/')))
        mkdir('./uploads/archive/', '0777');

    if (strpos($filename, lang('invoice')) !== false)
    {
        $CI = &get_instance();
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter('<div id="footer">' . $CI->Mdl_settings->settings['pdf_invoice_footer'] . '</div>');
    }

    $invoice_array = array();
    $mpdf->WriteHTML($html);

    if ($stream)
    {
        if (!$isInvoice)
        {
            return $mpdf->Output($filename . '.pdf', 'I');
        }

        foreach (glob('./uploads/archive/*' . $filename . '.pdf') as $file)
        {
            array_push($invoice_array, $file);
        }

        if (!empty($invoice_array) AND $isGuest)
        {
            rsort($invoice_array);
            header('Content-type: application/pdf');
            return readfile($invoice_array[0]);
        }
        else
        if ($isGuest)
        {
            //todo flashdata is deleted between requests
            //$CI->session->flashdata('alert_error', 'sorry no Invoice found!');
            redirect('guest/view/invoice/' . end($CI->uri->segment_array()));
        }

        $mpdf->Output('./uploads/archive/' . date('Y-m-d') . '_' . $filename . '.pdf', 'F');
        return $mpdf->Output($filename . '.pdf', 'I');
    }
    else
    {
        if ($isInvoice)
        {
            foreach (glob('./uploads/archive/*' . $filename . '.pdf') as $file)
            {
                array_push($invoice_array, $file);
            }
            if (!empty($invoice_array) && !is_null($isGuest))
            {
                rsort($invoice_array);
                return $invoice_array[0];
            }
            $mpdf->Output('./uploads/archive/' . date('Y-m-d') . '_' . $filename . '.pdf', 'F');
            return './uploads/archive/' . date('Y-m-d') . '_' . $filename . '.pdf';
        }
        $mpdf->Output('./uploads/temp/' . $filename . '.pdf', 'F');

        // DELETE OLD TEMP FILES - Housekeeping
        // Delete any files in temp/ directory that are >1 hrs old
        $interval = 3600;

        if ($handle = @opendir(preg_replace('/\/$/', '', './uploads/temp/')))
        {
            while (false !== ($file = readdir($handle)))
            {
                if (($file != "..") && ($file != ".") && !is_dir($file) && ((filemtime('./uploads/temp/' . $file) + $interval) < time()) && (substr($file, 0, 1) !== '.') && ($file != 'remove.txt'))// mPDF 5.7.3
                {
                    unlink('./uploads/temp/' . $file);
                }
            }
            closedir($handle);
        }

        //==============================================================================================================

        return './uploads/temp/' . $filename . '.pdf';

    }

}

function get_field_company($field)
{
    $CI = &get_instance();
    $company = $CI->db->query('SELECT * FROM ip_companies WHERE company_id = "' . $CI->session->userdata('company_id') . '"')->result_array();

    foreach ($company as $c)
    {
        return $c[$field];
    }
}

function get_field_user($field)
{
    $CI = &get_instance();
    $company = $CI->db->query('SELECT * FROM ip_users WHERE company_id = "' . $CI->session->userdata('company_id') . '" AND user_type=3')->result_array();

    foreach ($company as $c)
    {
        return $c[$field];
    }
}

function get_field_invoice($field, $id)
{
    $CI = &get_instance();
    $company = $CI->db->query('SELECT * FROM ip_invoices WHERE invoice_id = "' . $id . '"')->result_array();

    foreach ($company as $c)
    {
        return $c[$field];
    }
}

function pdf_create_custom_template($html, $filename, $stream = TRUE, $password = NULL, $isInvoice = NULL, $isGuest = NULL, $invoice_id)
{
    $CI = &get_instance();
    $company = $CI->session->userdata('company_id');

    require_once (APPPATH . 'helpers/mpdf/mpdf.php');

    $mpdf = new mPDF('utf-8', 'A4', '', '', 10, 10, 15, 15, 0, 10);
    $mpdf->useAdobeCJK = true;
    $mpdf->SetProtection(array(
        'copy',
        'print'
    ), $password, $password);

    if (!(is_dir('./uploads/archive/') OR is_link('./uploads/archive/')))
        mkdir('./uploads/archive/', '0777');

    if (strpos($filename, lang('invoice')) !== false)
    {
        $CI = &get_instance();
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter('<div id="footer">
	        <table>
	            <tr>
					<td valign="top" width="20%">
	                    <p>{PAGENO} page of {nb} </p>
	                </td>
	                <td valign="top" width="20%">
	                    <p>' . get_field_company('company_name') . '</p>
	                    <p>' . get_field_company('company_address') . '</p>
	                </td>
	                <td valign="top" width="20%">
	                    <p>Reg. code: ' . get_field_company('company_code') . '</p>
	                    <p>VAT: ' . get_field_company('company_vatregnumber') . '</p>
	                    <p>E-mail: ' . get_field_user('user_email') . '</p>
	                </td>
	                <td valign="top" width="20%">
	                    <p>Bank Acc: ' . get_field_company('company_bank_bic') . '</p>
	                    <p>IBAN: ' . get_field_company('company_iban') . '</p>
	                </td>
	                <td valign="top">
	                    <p>' . get_field_user('user_web') . '</p>
	                </td>
	            </tr>
	        </table>
	    </div>');
    }

    $invoice_array = array();

    $mpdf->WriteHTML($html);

    if ($stream)
    {
        if (!$isInvoice)
        {
            return $mpdf->Output($filename . '.pdf', 'I');
        }

        foreach (glob('./uploads/archive/*' . $filename . '.pdf') as $file)
        {
            array_push($invoice_array, $file);
        }

        if (!empty($invoice_array) AND $isGuest)
        {
            rsort($invoice_array);
            header('Content-type: application/pdf');
            return readfile($invoice_array[0]);
        }
        else
        {
            if ($isGuest)
            {
                //todo flashdata is deleted between requests
                //$CI->session->flashdata('alert_error', 'sorry no Invoice found!');
                redirect('guest/view/invoice/' . end($CI->uri->segment_array()));
            }
        }

        $mpdf->Output('./uploads/archive/' . date('Y-m-d') . '_' . $filename . '.pdf', 'F');
        return $mpdf->Output($filename . '.pdf', 'I');
    }
    else
    {
        if ($isInvoice)
        {
            foreach (glob('./uploads/archive/*' . $filename . '.pdf') as $file)
            {
                array_push($invoice_array, $file);
            }

            if (!empty($invoice_array) && !is_null($isGuest))
            {
                rsort($invoice_array);
                return $invoice_array[0];
            }

            $mpdf->Output('./uploads/archive/' . date('Y-m-d') . '_' . $filename . '.pdf', 'F');
            return './uploads/archive/' . date('Y-m-d') . '_' . $filename . '.pdf';
        }

        $mpdf->Output('./uploads/temp/' . $filename . '.pdf', 'F');

        // DELETE OLD TEMP FILES - Housekeeping

        // Delete any files in temp/ directory that are >1 hrs old

        $interval = 3600;

        if ($handle = @opendir(preg_replace('/\/$/', '', './uploads/temp/')))
        {
            while (false !== ($file = readdir($handle)))
            {
                if (($file != "..") && ($file != ".") && !is_dir($file) && ((filemtime('./uploads/temp/' . $file) + $interval) < time()) && (substr($file, 0, 1) !== '.') && ($file != 'remove.txt'))
                // mPDF 5.7.3
                {
                    unlink('./uploads/temp/' . $file);
                }
            }
            closedir($handle);
        }

        return './uploads/temp/' . $filename . '.pdf';
    }
}

function pdf_create_report_template($html, $filename, $stream = TRUE, $password = NULL, $isInvoice = NULL, $isGuest = NULL)
{
    $CI = &get_instance();
    $company = $CI->session->userdata('company_id');

    require_once (APPPATH . 'helpers/mpdf/mpdf.php');

    $mpdf = new mPDF('', '', '', '', 0, 0, 7, 0);
    $mpdf->useAdobeCJK = true;

    //$mpdf->SetAutoFont();
    $mpdf->SetProtection(array(
        'copy',
        'print'
    ), $password, $password);

    if (!(is_dir('/usr/share/nginx/html/uploads/archive/') OR is_link('/usr/share/nginx/html/uploads/archive/')))
        mkdir('/usr/share/nginx/html/uploads/archive/', '0777');

    if (strpos($filename, lang('invoice')) !== false)
    {
        $CI = &get_instance();
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter('
<div id="footer">
    <table id="tablefooter">
      <tr>
         <td>
            <p>{PAGENO} page of {nb} </p>
         </td>
         <td>
            <p>' . get_field_company('company_name') . '</p>
            <p>' . get_field_company('company_address') . '</p>
         </td>
         <td>
            Reg. code: ' . get_field_company('company_code') . '<br>
            VAT: ' . get_field_company('company_vatregnumber') . '<br>
            E-mail: ' . get_field_user('user_email') . '<br>
         </td>
         <td>
            Bank Acc: ' . get_field_company('company_bank_bic') . '<br>
            IBAN: ' . get_field_company('company_iban') . '<br>
         </td>
         <td>
            ' . get_field_user('user_web') . '<br>
         </td>
      </tr>
    </table>
</div>
                                ');
    }

    $invoice_array = array();
    $mpdf->WriteHTML($html);

    if ($stream)
    {
        if (!$isInvoice)
        {
            return $mpdf->Output($filename . '.pdf', 'I');
        }

        foreach (glob('/usr/share/nginx/html/uploads/archive/*' . $filename . '.pdf') as $file)
        {
            array_push($invoice_array, $file);
        }

        if (!empty($invoice_array) AND $isGuest)
        {
            rsort($invoice_array);
            header('Content-type: application/pdf');
            return readfile($invoice_array[0]);
        }
        else
        if ($isGuest)
        {
            //todo flashdata is deleted between requests
            //$CI->session->flashdata('alert_error', 'sorry no Invoice found!');
            redirect('guest/view/invoice/' . end($CI->uri->segment_array()));
        }

        $mpdf->Output('/usr/share/nginx/html/uploads/archive/' . date('Y-m-d') . '_' . $filename . '.pdf', 'F');
        return $mpdf->Output($filename . '.pdf', 'I');
    }
    else
    {
        if ($isInvoice)
        {
            foreach (glob('/usr/share/nginx/html/uploads/archive/*' . $filename . '.pdf') as $file)
            {
                array_push($invoice_array, $file);
            }

            if (!empty($invoice_array) && !is_null($isGuest))
            {
                rsort($invoice_array);
                return $invoice_array[0];
            }

            $mpdf->Output('/usr/share/nginx/html/uploads/archive/' . date('Y-m-d') . '_' . $filename . '.pdf', 'F');
            return '/usr/share/nginx/html/uploads/archive/' . date('Y-m-d') . '_' . $filename . '.pdf';
        }

        $mpdf->Output('/usr/share/nginx/html/uploads/temp/' . $filename . '.pdf', 'F');

        // DELETE OLD TEMP FILES - Housekeeping
        // Delete any files in temp/ directory that are >1 hrs old

        $interval = 3600;

        if ($handle = @opendir(preg_replace('/\/$/', '', '/usr/share/nginx/html/uploads/temp/')))
        {
            while (false !== ($file = readdir($handle)))
            {
                if (($file != "..") && ($file != ".") && !is_dir($file) && ((filemtime('/usr/share/nginx/html/uploads/temp/' . $file) + $interval) < time()) && (substr($file, 0, 1) !== '.') && ($file != 'remove.txt'))// mPDF 5.7.3
                {
                    unlink('/usr/share/nginx/html/uploads/temp/' . $file);
                }
            }
            closedir($handle);
        }

        //==============================================================================================================

        return '/usr/share/nginx/html/uploads/temp/' . $filename . '.pdf';
    }
}
