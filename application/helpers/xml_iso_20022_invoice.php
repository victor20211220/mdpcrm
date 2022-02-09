<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


function xml_iso_20022_invoice($params, $from_date, $to_date){

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
    $CI->load->model('invoices/Mdl_invoice_tax_rates');
    $CI->load->model('Mdl_payment_methods');
    $CI->load->library('encrypt');
    $CI->load->helper('mpdf');

	//check fields
	$check_res = check_mandatory_fields($invoices);

	if($check_res == false)
	   return;


    $xml = new DOMDocument("1.0");
	$xml_E_Invoice = $xml->createElement("E_Invoice");
	$xml->appendChild($xml_E_Invoice);

	/*XML HEADER PART*/
	/**********************************/
	/**********************************/
	$xml_Header = $xml->createElement("Header");
		//header date
		$xml_Header_date = $xml->createElement("Date");
		$xml_Header_date->nodeValue = date('Y-m-d');
		$xml_Header->appendChild($xml_Header_date);

		//unique_val
		$xml_Header_FileId = $xml->createElement("FileId");
		$xml_Header_FileId->nodeValue = time();
		$xml_Header->appendChild($xml_Header_FileId);

		//AppId
		$xml_Header_AppId = $xml->createElement("AppId");
		$xml_Header_AppId->nodeValue = 'EINVOICE';
		$xml_Header->appendChild($xml_Header_AppId);

		//Version
		$xml_Header_Version = $xml->createElement("Version");
		$xml_Header_Version->nodeValue = '1.1';
		$xml_Header->appendChild($xml_Header_Version);

	$xml_E_Invoice->appendChild($xml_Header);
	/*XML INVOICE PART*/
	/**********************************/
	/**********************************/

	foreach($invoices as $invoice){

		//fields check
		$xml_Invoice = $xml->createElement("Invoice");
		$xml_E_Invoice->appendChild($xml_Invoice);
		$xml_Invoice->setAttribute('invoiceId', $invoice->invoice_number);
		$xml_Invoice->setAttribute('channelId', $invoice->client_swift);
		$xml_Invoice->setAttribute('channelAddress', $invoice->client_iban);
		$xml_Invoice->setAttribute('invoiceGlobUniqId', $invoice->invoice_number);
		$xml_Invoice->setAttribute('sellerContractId', 'not specified');
		$xml_Invoice->setAttribute('sellerRegNumber', $invoice->company_code);


		//parties
		$xml_InvoiceParties = $xml->createElement("InvoiceParties");
		$xml_Invoice->appendChild($xml_InvoiceParties);

			//seller party
			$xml_SellerParty = $xml->createElement("SellerParty");
			$xml_InvoiceParties->appendChild($xml_SellerParty);
			    // /Name
				$xml_Name = $xml->createElement("Name");
				$xml_Name->nodeValue = $invoice->company_name;
				$xml_SellerParty->appendChild($xml_Name);
				//regNumber
				$xml_regNumber = $xml->createElement("regNumber");
				$xml_regNumber->nodeValue = $invoice->company_code;
				$xml_SellerParty->appendChild($xml_Name);
			    //regNumber
				$xml_company_vatregnumber = $xml->createElement("VATRegNumber");
				$xml_company_vatregnumber->nodeValue = $invoice->company_vatregnumber;
				$xml_SellerParty->appendChild($xml_company_vatregnumber);


			//buyer/client party
			$xml_BuyerParty = $xml->createElement("BuyerParty");
			$xml_InvoiceParties->appendChild($xml_BuyerParty);
			    // /Name
				$xml_Name = $xml->createElement("Name");
				$xml_Name->nodeValue = $invoice->client_name;
				$xml_BuyerParty->appendChild($xml_Name);
			    //regNumber
				$xml_company_vatregnumber = $xml->createElement("VATRegNumber");
				$xml_company_vatregnumber->nodeValue = $invoice->client_vat_id;
				$xml_BuyerParty->appendChild($xml_company_vatregnumber);

				//contact data

					$xml_BuyerContactDataRecord = set_BuyerContactDataRecord($xml,$invoice);
					$xml_BuyerParty->appendChild($xml_BuyerContactDataRecord);

					$xml_address_record = get_AddressRecord($xml,$invoice);
					$xml_BuyerParty->appendChild($xml_address_record);

		//(InvoiceInformation)
		$xml_InvoiceInformation =  gen_InvoiceInformation($xml,$invoice);
		$xml_Invoice->appendChild($xml_InvoiceInformation);

		//(InvoiceInformation)
		$xml_InvoiceSumGroup =  gen_InvoiceSumGroup($xml,$invoice);
		$xml_Invoice->appendChild($xml_InvoiceSumGroup);

		//(InvoiceInformation)
		$xml_InvoiceItem =  gen_InvoiceItem($xml,$invoice);
		$xml_Invoice->appendChild($xml_InvoiceItem);

		//(PaymentInfo)
		$xml_PaymentInfo =  gen_PaymentInfo($xml,$invoice);
		$xml_Invoice->appendChild($xml_PaymentInfo);

		$total_invoices_amount+=$invoice->invoice_total;

	}
	//*XML FOOTER PART*/
	/**********************************/
	/**********************************/
	$xml_Footer = $xml->createElement("Footer");
	$xml_E_Invoice->appendChild($xml_Footer);

	$xml_footer_data = $xml->createElement("TotalNumberInvoices");
	$xml_footer_data->nodeValue = count($invoices);
	$xml_Footer->appendChild($xml_footer_data);

	$xml_footer_data = $xml->createElement("TotalAmount");
	$xml_footer_data->nodeValue = $total_invoices_amount;
	$xml_Footer->appendChild($xml_footer_data);

	$xml->formatOutput = true;

	$filename = lang('invoices') . '_' . join('_', [$from_date, $to_date]) . '.xml';
	header('Content-type: text/xml');
	header('Content-Disposition: attachment; filename="' . $filename . '"');
	echo $xml->saveXML();

	//$xml->save("mybooks.xml") or die("Error");
	exit;
}

function check_mandatory_fields($invoices){

	$alerts = '';
	$a_c = 0;
	$check = true;

	foreach($invoices as $invoice){

		if($a_c>=5) continue;

		//fields check

		$CI    = &get_instance();
		if($invoice->client_swift==''){
			$alerts.= sprintf(lang('set_swift'), $invoice->client_name, site_url('clients/form/' . $invoice->client_id)).'<br>'; $check=false;$a_c++;
		}
		if($invoice->client_iban==''){
			$alerts.= sprintf(lang('set_iban'), $invoice->client_name, site_url('clients/form/' . $invoice->client_id)).'<br>'; $check=false;$a_c++;
		}
        if($invoice->company_code==''){
			$alerts.= sprintf(lang('set_company_code_xml_error'), $invoice->client_name, site_url('clients/form/' . $invoice->client_id)).'<br>'; $check=false;$a_c++;
		}
		if($invoice->client_address_1==''){
			$alerts.= sprintf(lang('set_postal_address1_xml_error'), $invoice->client_name, site_url('clients/form/' . $invoice->client_id)).'<br>'; $check=false;$a_c++;
		}
		if($invoice->client_city==''){
			$alerts.= sprintf(lang('set_city_xml_error'), $invoice->client_name, site_url('clients/form/' . $invoice->client_id)).'<br>'; $check=false;$a_c++;
		}
		if($invoice->company_iban==''){
			$alerts.= lang('set_company_iban_xml_error').'<br>'; $check=false;$a_c++;
		}
	}



	if($alerts!='')
		$CI->session->set_flashdata('alert_error', $alerts); ;

	return  $check;

}

function get_AddressRecord($xml, $invoice){

	$xml_address = $xml->createElement("MailAddress");

	$xml_address_data = $xml->createElement("PostalAddress1");
	$xml_address_data->nodeValue = $invoice->client_address_1;
	$xml_address->appendChild($xml_address_data);

	$xml_address_data = $xml->createElement("PostalAddress2");
	$xml_address_data->nodeValue = $invoice->client_address_2;
	$xml_address->appendChild($xml_address_data);

	$xml_address_data = $xml->createElement("City");
	$xml_address_data->nodeValue = $invoice->client_city;
	$xml_address->appendChild($xml_address_data);

	$xml_address_data = $xml->createElement("PostalCode");
	$xml_address_data->nodeValue = $invoice->client_zip;
	$xml_address->appendChild($xml_address_data);

	$xml_address_data = $xml->createElement("Country");
	$xml_address_data->nodeValue = $invoice->client_country;
	$xml_address->appendChild($xml_address_data);

	return $xml_address;
}

function set_BuyerContactDataRecord($xml, $invoice){


	$xml_contact_data_record = $xml->createElement("ContactData");

	$xml_buyer_data = $xml->createElement("ContactName");
	$xml_buyer_data->nodeValue = $invoice->client_name;
	$xml_contact_data_record->appendChild($xml_buyer_data);
	$xml_buyer_data = $xml->createElement("PhoneNumber");
	$xml_buyer_data->nodeValue = $invoice->client_phone;
	$xml_contact_data_record->appendChild($xml_buyer_data);
	$xml_buyer_data = $xml->createElement("FaxNumber");
	$xml_buyer_data->nodeValue = $invoice->client_fax;
	$xml_contact_data_record->appendChild($xml_buyer_data);
	$xml_buyer_data = $xml->createElement("URL");
	$xml_buyer_data->nodeValue = $invoice->client_web;
	$xml_contact_data_record->appendChild($xml_buyer_data);
	$xml_buyer_data = $xml->createElement("MailAddress");
	$xml_buyer_data->nodeValue = $invoice->client_email;
	$xml_contact_data_record->appendChild($xml_buyer_data);

	return $xml_contact_data_record;

}

function gen_InvoiceInformation($xml, $invoice){

	$CI = &get_instance();
	$xml_invoice_information = $xml->createElement("InvoiceInformation");

	$xml_inv_data = $xml->createElement("Type");
	if($invoice->invoice_total>=0){
			$status = 'DEB';
		}else{
			$status = 'CRE';
		}
	$xml_inv_data->setAttribute('Type', $status);
	$xml_invoice_information->appendChild($xml_inv_data);

	$xml_inv_data = $xml->createElement("e-invoice");
	$xml_inv_data->nodeValue = $invoice->client_email;
	$xml_invoice_information->appendChild($xml_inv_data);

    $xml_inv_data = $xml->createElement("InvoiceNumber");
	$xml_inv_data->nodeValue = $invoice->invoice_number;
	$xml_invoice_information->appendChild($xml_inv_data);

	$xml_inv_data = $xml->createElement("InvoiceDate");
	$xml_inv_data->nodeValue = $invoice->invoice_date_created;
	$xml_invoice_information->appendChild($xml_inv_data);

	$xml_inv_data = $xml->createElement("DueDate");
	$xml_inv_data->nodeValue = $invoice->invoice_date_due;
	$xml_invoice_information->appendChild($xml_inv_data);

	$payment_meth = $CI->Mdl_payment_methods->filter_where('ip_payment_methods.company_id', $CI->session->userdata('company_id'))
									->get()
									->result();

	if($invoice->payment_method!=0 AND $invoice->payment_method !=''){
		$payment_meth = $CI->Mdl_payment_methods->filter_where('ip_payment_methods.company_id', $CI->session->userdata('company_id'))
		                                        ->filter_where('ip_payment_methods.payment_method_id', $invoice->payment_method)
									->get()
									->result();
		$xml_inv_data = $xml->createElement("PaymentMethod");
		$xml_inv_data->nodeValue = $payment_meth[0]->payment_method_name;
		$xml_invoice_information->appendChild($xml_inv_data);
	}


	return $xml_invoice_information;


}

function gen_InvoiceSumGroup($xml,$invoice){

	$CI = &get_instance();
	$CI->load->model('settings/Mdl_settings');
	$xml_invoice_sum_group = $xml->createElement("InvoiceSumGroup");

	$xml_inv_data = $xml->createElement("TotalSum");
	$xml_inv_data->nodeValue = $invoice->invoice_total;
	$xml_invoice_sum_group->appendChild($xml_inv_data);

	$xml_inv_data = $xml->createElement("InvoiceSum");
	$xml_inv_data->nodeValue = $invoice->invoice_total-($invoice->invoice_tax_total+$invoice->invoice_item_tax_total);
	$xml_invoice_sum_group->appendChild($xml_inv_data);

	$xml_inv_data = $xml->createElement("TotalVATSum");
	$xml_inv_data->nodeValue = $invoice->invoice_tax_total+$invoice->invoice_item_tax_total;
	$xml_invoice_sum_group->appendChild($xml_inv_data);

	$xml_inv_data = $xml->createElement("Currency");
	$xml_inv_data->nodeValue = strtoupper($CI->Mdl_settings->setting('currency_symbol'));
	$xml_invoice_sum_group->appendChild($xml_inv_data);

	return $xml_invoice_sum_group;
}

function gen_InvoiceItem($xml,$invoice){

	$CI = &get_instance();
	$CI->load->model('invoices/Mdl_items');
	$CI->load->model('Mdl_tax_rates');
	$items = $CI->Mdl_items->where('invoice_id', $invoice->invoice_id)->get()->result();
	$tax_rates    = $CI->Mdl_tax_rates->filter_where('ip_tax_rates.company_id', $CI->session->userdata('company_id'))
									 ->get()
									 ->result();

	$xml_invoice_item_root = $xml->createElement("InvoiceItem");
	$xml_invoice_item_group = $xml->createElement("InvoiceItemGroup");
	$xml_invoice_item_root->appendChild($xml_invoice_item_group);

	foreach($items as $k=>$item){

		$item_tax_rate = lang('none');
		foreach ($tax_rates as $tax_rate){
		             if($item->item_tax_rate_id == $tax_rate->tax_rate_id){
		                       $item_tax_rate = $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name;
		                }
		}

		$xml_invoice_item = $xml->createElement("ItemEntry");

		$xml_inv_data = $xml->createElement("Description");
		$xml_inv_data->nodeValue = $item->item_name;
		$xml_invoice_item->appendChild($xml_inv_data);

		$xml_inv_data = $xml->createElement("SellerProductId");
		$xml_inv_data->nodeValue = $item->item_id;
		$xml_invoice_item->appendChild($xml_inv_data);

		$xml_inv_data = $xml->createElement("ItemPrice");
		$xml_inv_data->nodeValue = $item->item_price;
		$xml_invoice_item->appendChild($xml_inv_data);

		$xml_inv_data = $xml->createElement("ItemSum");
		$xml_inv_data->nodeValue = $item->item_subtotal;
		$xml_invoice_item->appendChild($xml_inv_data);

		$xml_inv_data = $xml->createElement("ItemTotal");
		$xml_inv_data->nodeValue = $item->item_total;
		$xml_invoice_item->appendChild($xml_inv_data);

		$xml_inv_data_add_rec =  $xml->createElement("Addition");
		$xml_inv_data_add_rec -> setAttribute('addCode', 'DSC');

			$xml_inv_data_add_rec_cont = $xml->createElement("AddContent");
			$xml_inv_data_add_rec_cont->nodeValue = 'Discount: '.$item->item_discount_amount;
			$xml_inv_data_add_rec->appendChild($xml_inv_data_add_rec_cont);
			$xml_inv_data_add_rec_cont = $xml->createElement("AddSum");
			$xml_inv_data_add_rec_cont->nodeValue = $item->item_discount;
			$xml_inv_data_add_rec->appendChild($xml_inv_data_add_rec_cont);

		$xml_invoice_item->appendChild($xml_inv_data_add_rec);
		$xml_invoice_item_group->appendChild($xml_invoice_item);
	}



	$xml_invoice_item_total_group = $xml->createElement("InvoiceItemTotalGroup");
	$xml_invoice_item_root->appendChild($xml_invoice_item_total_group);

    $xml_inv_data_add_rec_cont = $xml->createElement("InvoiceItemTotalSum");
	$xml_inv_data_add_rec_cont->nodeValue = $invoice->invoice_item_subtotal;
	$xml_invoice_item_total_group->appendChild($xml_inv_data_add_rec_cont);
	$xml_inv_data_add_rec_cont = $xml->createElement("InvoiceItemTotal");
	$xml_inv_data_add_rec_cont->nodeValue = $invoice->invoice_item_subtotal+$invoice->invoice_item_tax_total;
	$xml_invoice_item_total_group->appendChild($xml_inv_data_add_rec_cont);


	return $xml_invoice_item_root;

}

function gen_PaymentInfo($xml,$invoice){


	$CI = &get_instance();
	$CI->load->model('settings/Mdl_settings');
	$xml_PaymentInfo = $xml->createElement("PaymentInfo");

	$xml_pay_data = $xml->createElement("Currency");
	$xml_pay_data->nodeValue = 'EUR';
	$xml_PaymentInfo->appendChild($xml_pay_data);

	$xml_pay_data = $xml->createElement("PaymentDescription");
	$xml_pay_data->nodeValue = 'Invoice '.$invoice->invoice_number;
	$xml_PaymentInfo->appendChild($xml_pay_data);

	$xml_pay_data = $xml->createElement("Payable");
	$xml_pay_data->nodeValue = 'YES';
	$xml_PaymentInfo->appendChild($xml_pay_data);

	$xml_pay_data = $xml->createElement("PayDueDate");
	$xml_pay_data->nodeValue = $invoice->invoice_date_due;
	$xml_PaymentInfo->appendChild($xml_pay_data);

	$xml_pay_data = $xml->createElement("PaymentTotalSum");
	$xml_pay_data->nodeValue = $invoice->invoice_total;
	$xml_PaymentInfo->appendChild($xml_pay_data);

	$xml_pay_data = $xml->createElement("PaymentId");
	$xml_pay_data->nodeValue = $invoice->invoice_number;
	$xml_PaymentInfo->appendChild($xml_pay_data);

	$xml_pay_data = $xml->createElement("PayToAccount");
	$xml_pay_data->nodeValue = $invoice->company_iban;
	$xml_PaymentInfo->appendChild($xml_pay_data);

	$xml_pay_data = $xml->createElement("PayToName");
	$xml_pay_data->nodeValue = $invoice->company_name;
	$xml_PaymentInfo->appendChild($xml_pay_data);

	return $xml_PaymentInfo;

}

?>
