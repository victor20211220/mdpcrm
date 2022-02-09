<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


function raw_xml_invoice($params, $from_date, $to_date){

	$CI 					= &get_instance();
	$userName 				= $CI->session->userdata('user_name');
	$company_id 			= $CI->session->userdata('company_id');
    $invoices 				= $params['invoices'];
	$export_type 			= $params['export_type'];
	$invoice_statuses 		= $CI->Mdl_invoices->statuses();
	$currency_symbol    	= $CI->Mdl_settings->setting('currency_symbol');
	$total_invoices_amount  = 0;
	$new_world_order    = $params['new_world_order'];
	$params['mysql_cols'] = $CI->Mdl_invoices->get_cols_name_for_export();


	if($params['additional_options']!=1)
		{   $new_world_order = array();
			foreach($params['mysql_cols'] as $k=>$v){
				$new_world_order[]=$k;
			}
		}



	/** Include mpdf  */
    $CI->load->model(
            array(
                'Mdl_items',
                'Mdl_tax_rates',
                'Mdl_payment_methods',
                'Mdl_invoice_tax_rates',
                'Mdl_custom_fields',
                'Mdl_item_lookups'
            )
        );
    $CI->load->library('encrypt');
    $CI->load->helper('mpdf');

	//check fields


	$xml = new DOMDocument("1.0");
	$xml_E_Invoice = $xml->createElement("Invoices");
	$xml->appendChild($xml_E_Invoice);

	foreach($invoices as $invoice){

		//create a invoice element
		$xml_Invoice = $xml->createElement("Invoice");
		$xml_E_Invoice->appendChild($xml_Invoice);

		$tax_rates    = $CI->Mdl_tax_rates->filter_where('ip_tax_rates.company_id', $CI->session->userdata('company_id'))
									 ->get()
									 ->result();

		$payment_meth = $CI->Mdl_payment_methods->filter_where('ip_payment_methods.company_id', $CI->session->userdata('company_id'))
									->get()
									->result();

		$items      		= $CI->Mdl_items->where('invoice_id', $invoice->invoice_id)->get()->result();
        $tax_rates 			= $tax_rates;//$this->Mdl_tax_rates->get()->result(),
        $invoice_tax_rates 	= $CI->Mdl_invoice_tax_rates->where('invoice_id', $invoice->invoice_id)->get()->result();
        $payment_methods 	= $payment_meth;//$this->Mdl_payment_methods->get()->result(),
        $custom_fields 		= $CI->Mdl_custom_fields->by_table('ip_invoice_custom')->result();
        $item_lookups 		= $CI->Mdl_item_lookups->get()->result();

		foreach($new_world_order as $row_index_c){
							//in this case we have a problem ... something to code

							if($params['mysql_cols'][$row_index_c]['type']=='Item') continue;

							//here we handle exceptions - item_tax_rate
							$data_to_fill= '';
							//here we handle exceptions - item_tax_rate
							if($params['mysql_cols'][$row_index_c]['type']=='Invoice' && $params['mysql_cols'][$row_index_c]['col']=='invoice_status_id')
							   $data_to_fill = $invoice_statuses[$invoice->invoice_status_id]['label'];
							else
							   $data_to_fill = ${strtolower($params['mysql_cols'][$row_index_c]['type'])}->{$params['mysql_cols'][$row_index_c]['col']};

							$xml_elem = $xml->createElement($params['mysql_cols'][$row_index_c]['col']);
							$xml_elem->nodeValue = $data_to_fill;
							$xml_Invoice->appendChild($xml_elem);
			}



		//create an items
		$xml_Items = $xml->createElement("Items");
		$xml_Invoice->appendChild($xml_Items);

		foreach($items as $k=>$item){

			$item_tax_rate = lang('none');
			foreach ($tax_rates as $tax_rate) {
	                             if($item->item_tax_rate_id == $tax_rate->tax_rate_id){
	                             	$item_tax_rate = $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name;
	                             }
			}

			$xml_Item = $xml->createElement("Item");
		    $xml_Items->appendChild($xml_Item);

			foreach($new_world_order as $row_index_c){
							//in this case we have a problem ... something to code

							if($params['mysql_cols'][$row_index_c]['type']=='Invoice') continue;

							//here we handle exceptions - item_tax_rate
							$data_to_fill= '';
							//here we handle exceptions - item_tax_rate
							if($params['mysql_cols'][$row_index_c]['type']=='Item' && $params['mysql_cols'][$row_index_c]['col']=='item_tax_rate')
							   $data_to_fill = ${$params['mysql_cols'][$row_index_c]['col']};
							else
							   $data_to_fill = ${strtolower($params['mysql_cols'][$row_index_c]['type'])}->{$params['mysql_cols'][$row_index_c]['col']};

							$xml_elem = $xml->createElement($params['mysql_cols'][$row_index_c]['col']);
							$xml_elem->nodeValue = $data_to_fill;
							$xml_Item->appendChild($xml_elem);
			}

		}

	}

	$filename = lang('invoices') . '_' . join('_', [$from_date, $to_date]) . '.xml';
	header('Content-type: text/xml');
	header('Content-Disposition: attachment; filename="' . $filename . '"');
	echo $xml->saveXML();

	//$xml->save("mybooks.xml") or die("Error");
	exit;




    $xml = new DOMDocument("1.0");
	$xml_E_Invoice = $xml->createElement("Invoices");
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
	header('Content-type: text/xml');
	header('Content-Disposition: attachment; filename="Invoices.xml"');
	echo $xml->saveXML();

	//$xml->save("mybooks.xml") or die("Error");
	exit;

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

class ArrayToXML
{
    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     * Based on: http://snipplr.com/view/3491/convert-php-array-to-xml-or-simple-xml-object-if-you-wish/
	 *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public static function toXml($data, $rootNodeName = 'data', &$xml=null)
    {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
    if ( ini_get('zend.ze1_compatibility_mode') == 1 ) ini_set ( 'zend.ze1_compatibility_mode', 0 );
    if ( is_null( $xml ) ) {
    	 $xml = simplexml_load_string(stripslashes("<?xml version='1.0' encoding='utf-8'?><root xmlns:example='http://example.namespace.com' version='1.0'></root>"));
	}

    // loop through the data passed in.
    foreach( $data as $key => $value ) {

        // no numeric keys in our xml please!
        $numeric = false;
        if ( is_numeric( $key ) ) {
            $numeric = 1;
            $key = $rootNodeName;
        }

        // delete any char not allowed in XML element names
        $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

		//check to see if there should be an attribute added (expecting to see _id_)
		$attrs = false;

		//if there are attributes in the array (denoted by attr_**) then add as XML attributes
		if ( is_array( $value ) ) {
			foreach($value as $i => $v ) {
				$attr_start = false;
				$attr_start = stripos($i, 'attr_');
				if ($attr_start === 0) {
					$attrs[substr($i, 5)] = $v; unset($value[$i]);
				}
			}
		}


        // if there is another array found recursively call this function
        if ( is_array( $value ) ) {

            if ( ArrayToXML::is_assoc( $value ) || $numeric ) {

                // older SimpleXMLElement Libraries do not have the addChild Method
                if (method_exists('SimpleXMLElement','addChild'))
                {
                    $node = $xml->addChild( $key, null, 'http://www.lcc.arts.ac.uk/' );
					if ($attrs) {
						foreach($attrs as $key => $attribute) {
							$node->addAttribute($key, $attribute);
						}
					}
                }

            }else{
                $node =$xml;
            }

            // recrusive call.
            if ( $numeric ) $key = 'anon';
            ArrayToXML::toXml( $value, $key, $node );
        } else {

                // older SimplXMLElement Libraries do not have the addChild Method
                if (method_exists('SimpleXMLElement','addChild'))
                {
                    $childnode = $xml->addChild( $key, $value, 'http://www.lcc.arts.ac.uk/' );
					if ($attrs) {
						foreach($attrs as $key => $attribute) {
							$childnode->addAttribute($key, $attribute);
						}
					}
                }
        }
    }

	// pass back as unformatted XML
	//return $xml->asXML('data.xml');

	// if you want the XML to be formatted, use the below instead to return the XML
	    $doc = new DOMDocument('1.0');
	    $doc->preserveWhiteSpace = false;
	    @$doc->loadXML( ArrayToXML::fixCDATA($xml->asXML()) );
	    $doc->formatOutput = true;
	    //return $doc->saveXML();
	    return $doc->save('data.xml');
	}

	public static function fixCDATA($string) {
		//fix CDATA tags
		$find[]     = '&lt;![CDATA[';
		$replace[] = '<![CDATA[';
		$find[]     = ']]&gt;';
		$replace[] = ']]>';

		$string = str_ireplace($find, $replace, $string);
		return $string;
	}

/**
 * Convert an XML document to a multi dimensional array
 * Pass in an XML document (or SimpleXMLElement object) and this recrusively loops through and builds a representative array
 *
 * @param string $xml - XML document - can optionally be a SimpleXMLElement object
 * @return array ARRAY
 */
	public static function toArray( $xml ) {
	    if ( is_string( $xml ) ) $xml = new SimpleXMLElement( $xml );
	    $children = $xml->children();
	    if ( !$children ) return (string) $xml;
	    $arr = array();
	    foreach ( $children as $key => $node ) {
	        $node = ArrayToXML::toArray( $node );

	        // support for 'anon' non-associative arrays
	        if ( $key == 'anon' ) $key = count( $arr );

	        // if the node is already set, put it into an array
	        if ( isset( $arr[$key] ) ) {
	            if ( !is_array( $arr[$key] ) || $arr[$key][0] == null ) $arr[$key] = array( $arr[$key] );
	            $arr[$key][] = $node;
	        } else {
	            $arr[$key] = $node;
	        }
	    }
	    return $arr;
	}

	// determine if a variable is an associative array
	public static function is_assoc( $array ) {
	    return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
	}
}

?>
