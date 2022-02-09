<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


function xml_iso_20022_invoice($params, $from_date, $to_date){
    //var_dump($params);die;
	$CI 					= &get_instance();
	$userName 				= $CI->session->userdata('user_name');
	$company_id 			= $CI->session->userdata('company_id');
    $invoices 				= $params['invoices'];
	$export_type 			= $params['export_type'];
	$invoice_statuses 		= $CI->Mdl_invoices->statuses();
	$currency_symbol    	= $CI->Mdl_settings->setting('currency_symbol');
	$total_invoices_amount  = 0;

	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);

	/** Include mpdf  */
    $CI->load->model('invoices/Mdl_invoices');
    //$CI->load->model('invoices/Mdl_invoice_tax_rates');
    $CI->load->model('Mdl_payment_methods');
    $CI->load->library('encrypt');
    //$CI->load->helper('mpdf');

	//check fields
	/*
	 * now, we have invoices created manually, where the company is my
	 * company and the client is the one that I must pay!
	 *
	 * the second ones, those received, when a user of this app
	 * from another company, inserts a invoice and my company reg number is
	 * his client reg number --- now, for this case, I must reverse the
	 * company with the client
	 * */

	$invoices = refresh_invoices($invoices);




	$check_res = check_mandatory_fields($invoices);

	//print_r($invoices);exit;

	if($check_res == false)
	   return;


    $xml = new DOMDocument("1.0");
	$Document = $xml->createElement("Document");
	$Document->setAttribute('xmlns', 'urn:iso:std:iso:20022:tech:xsd:pain.001.001.03');
	$Document->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
	$xml->appendChild($Document);

	$CstmrCdtTrfInitn = $xml->createElement("CstmrCdtTrfInitn");
	$Document->appendChild($CstmrCdtTrfInitn);
	/*XML HEADER PART*/
	/**********************************/
	/**********************************/
	$GrpHdr = $xml->createElement("GrpHdr");
		//unique_val
		$MsgId = $xml->createElement("MsgId");
		$MsgId->nodeValue = time();
		$GrpHdr->appendChild($MsgId);

		//header date
		$CreDtTm = $xml->createElement("CreDtTm");
		$CreDtTm->nodeValue = date('Y-m-d').'T'.date('h:i:s');
		$GrpHdr->appendChild($CreDtTm);

		$NbOfTxs = $xml->createElement("NbOfTxs");
		$NbOfTxs->nodeValue = count($invoices);
		$GrpHdr->appendChild($NbOfTxs);

		$CtrlSum = $xml->createElement("CtrlSum");
		$CtrlSum->nodeValue = total_amount_inv($invoices);
		$GrpHdr->appendChild($CtrlSum);

		$InitgPty = $xml->createElement("InitgPty");
			$Nm = $xml->createElement("Nm");
			$Nm->nodeValue = total_amount_inv($invoices);
			$InitgPty->appendChild($Nm);
		$GrpHdr->appendChild($InitgPty);

	$CstmrCdtTrfInitn->appendChild($GrpHdr);
	/*XML INVOICE PART*/
	/**********************************/
	/**********************************/
	$PmtInf = $xml->createElement("PmtInf");
		//unique_val
		$PmtInfId = $xml->createElement("PmtInfId");
		$PmtInfId->nodeValue = time();
		$PmtInf->appendChild($PmtInfId);

		//header date
		$PmtMtd = $xml->createElement("PmtMtd");
		$PmtMtd->nodeValue = 'TRF';
		$PmtInf->appendChild($PmtMtd);

		$NbOfTxs = $xml->createElement("NbOfTxs");
		$NbOfTxs->nodeValue = count($invoices);
		$PmtInf->appendChild($NbOfTxs);

		$PmtTpInf = $xml->createElement("PmtTpInf");
			$SvcLvl = $xml->createElement("SvcLvl");
				$Cd = $xml->createElement("Cd");
				$Cd->nodeValue = 'SEPA';
				$SvcLvl->appendChild($Cd);
			$PmtTpInf->appendChild($SvcLvl);
		$PmtInf->appendChild($PmtTpInf);

		$ReqdExctnDt = $xml->createElement("ReqdExctnDt");
		$ReqdExctnDt->nodeValue = date('Y-m-d');
		$PmtInf->appendChild($ReqdExctnDt);

		//Dbtr
		$Dbtr = $xml->createElement("Dbtr");
			$Nm = $xml->createElement("Nm");
			$Nm->nodeValue = $invoices[0]->company_name;
			$Dbtr->appendChild($Nm);
			$PstlAdr = $xml->createElement("PstlAdr");
			    $Ctry = $xml->createElement("Ctry");
				$Ctry->nodeValue = $invoices[0]->company_country;
				$PstlAdr->appendChild($Ctry);
				$AdrLine = $xml->createElement("AdrLine");
				$AdrLine->nodeValue = $invoices[0]->company_address;
				$PstlAdr->appendChild($AdrLine);
			$Dbtr->appendChild($PstlAdr);
		$PmtInf->appendChild($Dbtr);

		//DbtrAcct
		$DbtrAcct = $xml->createElement("DbtrAcct");
			$Id = $xml->createElement("Id");
				$IBAN = $xml->createElement("IBAN");
				$IBAN->nodeValue = $invoices[0]->company_iban;
				$Id->appendChild($IBAN);
			$DbtrAcct->appendChild($Id);
			$Ccy = $xml->createElement("Ccy");
			$Ccy->nodeValue = 'EUR';
			$DbtrAcct->appendChild($Ccy);
		$PmtInf->appendChild($DbtrAcct);

		//DbtrAgt
		$DbtrAgt = $xml->createElement("DbtrAgt");
			$FinInstnId = $xml->createElement("FinInstnId");
				$BIC = $xml->createElement("BIC");
				$BIC->nodeValue = $invoices[0]->company_bank_bic;
				$FinInstnId->appendChild($BIC);
			$DbtrAgt->appendChild($FinInstnId);
		$PmtInf->appendChild($DbtrAgt);

		//ChargeBearer
		$ChrgBr = $xml->createElement("ChrgBr");
		$ChrgBr->nodeValue = 'SLEV';
		$PmtInf->appendChild($ChrgBr);


	foreach($invoices as $invoice){

		//fields check
		$CdtTrfTxInf = $xml->createElement("CdtTrfTxInf");
		//PmtId
		$PmtId = $xml->createElement("PmtId");
			$InstrId = $xml->createElement("InstrId");
			$InstrId->nodeValue = time().'-'.$invoice->invoice_number;
			$PmtId->appendChild($InstrId);

			$EndToEndId = $xml->createElement("EndToEndId");
			$EndToEndId->nodeValue = $invoice->invoice_number;
			$PmtId->appendChild($EndToEndId);

		$CdtTrfTxInf->appendChild($PmtId);

		//Amt
		$Amt = $xml->createElement("Amt");
			$InstdAmt = $xml->createElement("InstdAmt");
			$InstdAmt->nodeValue = $invoice->invoice_balance;
			$InstdAmt->setAttribute('Ccy', 'EUR');
			$Amt->appendChild($InstdAmt);
		$CdtTrfTxInf->appendChild($Amt);

		//Cdtr
		$Cdtr = $xml->createElement("Cdtr");
			$Nm = $xml->createElement("Nm");
			$Nm->nodeValue = $invoice->client_name;
			$Cdtr->appendChild($Nm);

			$PstlAdr = $xml->createElement("PstlAdr");
			    $Ctry = $xml->createElement("Ctry");
				$Ctry->nodeValue = $invoice->client_country;
				$PstlAdr->appendChild($Ctry);

				$AdrLine = $xml->createElement("AdrLine");
				$AdrLine->nodeValue = $invoice->client_address_1;
			$PstlAdr->appendChild($AdrLine);
		   $Cdtr->appendChild($PstlAdr);
		$CdtTrfTxInf->appendChild($Cdtr);

		//Cdtr
		$CdtrAcct = $xml->createElement("CdtrAcct");
			    $Id = $xml->createElement("Id");
				$IBAN = $xml->createElement("IBAN");
				$IBAN->nodeValue = $invoice->client_iban;
			  	$Id->appendChild($IBAN);
			$CdtrAcct->appendChild($Id);

		$CdtTrfTxInf->appendChild($CdtrAcct);

		//RmtInf
		$RmtInf = $xml->createElement("RmtInf");
			$Strd = $xml->createElement("Strd");
				$CdtrRefInf = $xml->createElement("CdtrRefInf");
					$Tp = $xml->createElement("Tp");
						$CdOrPrtry = $xml->createElement("CdOrPrtry");
							$Cd = $xml->createElement("Cd");
							$Cd->nodeValue = 'SCOR';
							$CdOrPrtry->appendChild($Cd);
						$Tp->appendChild($CdOrPrtry);
					$CdtrRefInf->appendChild($Tp);
				$Strd->appendChild($CdtrRefInf);
			$RmtInf->appendChild($Strd);
		$CdtTrfTxInf->appendChild($RmtInf);

		$PmtInf->appendChild($CdtTrfTxInf);


	}

	$CstmrCdtTrfInitn->appendChild($PmtInf);
	//*XML FOOTER PART*/
	/**********************************/
	/**********************************/

	$xml->formatOutput = true;
	header('Content-type: text/xml');
	header('Content-Disposition: attachment; filename="' . /*implode('_', [$from_date, $to_date])*/rand() . '.xml"');
	echo $xml->saveXML();

	//$xml->save("mybooks.xml") or die("Error");
	exit;

}

function check_mandatory_fields($invoices){

	$alerts = '';
	$a_c = 0;
	$check = true;
	$CI    = &get_instance();

	if(count($invoices)==0)
		{$alerts.= lang('no_invoices_search'); $check=false;$a_c++;}

	foreach($invoices as $invoice){

		if($a_c>=5) continue;

		//fields check

		//if($invoice->is_received==0)continue;


		/*if($invoice->company_swift==''){
			$alerts.= sprintf(lang('set_swift'), $invoice->company_name, site_url('clients/form/' . $invoice->company_id)).'<br>'; $check=false;$a_c++;
		}
		if($invoice->company_iban==''){
			$alerts.= sprintf(lang('set_iban'), $invoice->company_name, site_url('clients/form/' . $invoice->company_id)).'<br>'; $check=false;$a_c++;
		}
        if($invoice->company_code==''){
			$alerts.= sprintf(lang('set_company_code_xml_error'), $invoice->company_name, site_url('clients/form/' . $invoice->company_id)).'<br>'; $check=false;$a_c++;
		}
		if($invoice->company_address_1==''){
			$alerts.= sprintf(lang('set_postal_address1_xml_error'), $invoice->company_name, site_url('clients/form/' . $invoice->company_id)).'<br>'; $check=false;$a_c++;
		}
		if($invoice->company_city==''){
			$alerts.= sprintf(lang('set_city_xml_error'), $invoice->company_name, site_url('clients/form/' . $invoice->company_id)).'<br>'; $check=false;$a_c++;
		}
		if($invoice->company_iban==''){
			$alerts.= lang('set_company_iban_xml_error').'<br>'; $check=false;$a_c++;
		}*/
	}



	if($alerts!='')
		$CI->session->set_flashdata('alert_error', $alerts); ;

	return  $check;

}

function total_amount_inv($invoices){

	$total = 0;

	foreach ($invoices as $invoice){
		 	$total+=$invoice->invoice_balance;
		}

	return $total;

}

function refresh_invoices($invoices){

	$CI = &get_instance();
	$CI->load->model('Mdl_companies');

	$company = $CI->Mdl_companies->get_array_by_id($CI->session->userdata('company_id'));
	$new_inv = array();

	foreach ($invoices as $invoice){

		//this case, when the invoice was created by another company in my sistem
		if($invoice->is_received==0)
		 {
		  $aux = $invoice->company_address;
		  $invoice->company_address = $company['company_address'];
		  $invoice->client_address_1 = $aux;

		  $aux = $invoice->company_name;
		  $invoice->company_name = $company['company_name'];
		  $invoice->client_name = $aux;

		  $aux = $invoice->company_country;
		  $invoice->company_country = $company['company_country'];
		  $invoice->client_country = $aux;

		  $aux = $invoice->company_iban;
		  $invoice->company_iban = $company['company_iban'];
		  $invoice->client_iban = $aux;

		  $invoice->company_bank_bic = $company['company_bank_bic'];

		  $aux = $invoice->company_iban;
		  $invoice->company_iban = $company['company_iban'];
		  $invoice->client_iban = $aux;}

		  $new_inv[] = $invoice;
		}

	return $new_inv;
}

?>
