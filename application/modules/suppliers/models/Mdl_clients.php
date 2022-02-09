<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_clients extends Response_Model

{

    public $table = 'ip_clients';

    public $primary_key = 'ip_clients.client_id';

    public $date_created_field = 'client_date_created';

    public $date_modified_field = 'client_date_modified';

    public function default_select()

    {

        $this->db->select('SQL_CALC_FOUND_ROWS ip_client_custom.*, ip_clients.*', FALSE);

    }

    public function default_join()

    {

        $this->db->join('ip_client_custom', 'ip_client_custom.client_id = ip_clients.client_id', 'left');

    }

    public function default_order_by()

    {

        $this->db->order_by('ip_clients.client_name');

    }

    public function validation_rules()

    {

        return array(

            'client_name' => array(

                'field' => 'client_name',

                'label' => lang('client_name'),

                'rules' => 'required'
            ),

            'client_reg_number' => array('field' => 'client_reg_number'),

            'company_id' => array('field' => 'client_name'),

            'client_active' => array('field' => 'client_active'),

            'client_address_1' => array('field' => 'client_address_1'),

            'client_address_2' => array('field' => 'client_address_2'),

            'client_city' => array('field' => 'client_city'),

            'client_state' => array('field' => 'client_state'),

            'client_zip' => array('field' => 'client_zip'),

            'client_country' => array('field' => 'client_country'),

            'client_phone' => array('field' => 'client_phone'),

            'client_fax' => array('field' => 'client_fax'),

            'client_mobile' => array('field' => 'client_mobile'),

            'client_email' => array('field' => 'client_email'),

            'client_web' => array('field' => 'client_web'),

            'client_vat_id' => array('field' => 'user_vat_id'),

            'client_tax_code' => array('field' => 'user_tax_code'),

            'client_swift' => array(

                'field' => 'client_swift',

                'label' => 'SWIFT',

                'rules' => 'required|callback_client_swift'
            ),

            'client_iban' => array(

                'field' => 'client_iban',

                'label' => 'IBAN',

                'rules' => 'required|callback_client_iban'
            )
        );

    }

    function client_iban($swift)
    {

        if (!verify_iban($swift, $machine_format_only = false))
        {

            $this->form_validation->set_message('client_iban', 'IBAN code is not valid', 'Hello World !');

            return false;

        }

    }

    function client_swift($swift)

    {

        $regexp = '/^([a-zA-Z]){4}([a-zA-Z]){2}([0-9a-zA-Z]){2}([0-9a-zA-Z]{3})?$/';

        //$isokay = (boolean) preg_match($regexp, $swiftbic);

        if (!preg_match($regexp, $swift))

        {

            $this->form_validation->set_message('client_swift', 'SWIFT code is not valid', 'Hello World !');

            return false;

        }

        else

        {

            return true;

        }

    }

    public function get_cols_name_for_import()
    {

        $cols = array(

            array(
                'col' => 'client_name',
                'name' => lang('client_name'),
                'required' => 1
            ),

            array(
                'col' => 'client_reg_number',
                'name' => lang('client_reg_number'),
                'required' => 1
            ),

            array(
                'col' => 'client_address_1',
                'name' => lang('street_address'),
                'required' => 0
            ),

            array(
                'col' => 'client_address_2',
                'name' => lang('street_address_2'),
                'required' => 0
            ),

            array(
                'col' => 'client_city',
                'name' => lang('city'),
                'required' => 0
            ),

            array(
                'col' => 'client_state',
                'name' => lang('state'),
                'required' => 0
            ),

            array(
                'col' => 'client_zip',
                'name' => lang('zip_code'),
                'required' => 0
            ),

            array(
                'col' => 'client_country',
                'name' => lang('country'),
                'required' => 0
            ),

            array(
                'col' => 'client_phone',
                'name' => lang('phone_number'),
                'required' => 0
            ),

            array(
                'col' => 'client_fax',
                'name' => lang('fax_number'),
                'required' => 0
            ),

            array(
                'col' => 'client_mobile',
                'name' => lang('mobile_number'),
                'required' => 0
            ),

            array(
                'col' => 'client_email',
                'name' => lang('email_address'),
                'required' => 1
            ),

            array(
                'col' => 'client_web',
                'name' => lang('web_address'),
                'required' => 0
            ),

            array(
                'col' => 'client_vat_id',
                'name' => lang('vat_id'),
                'required' => 0
            ),

            array(
                'col' => 'client_tax_code',
                'name' => lang('tax_code'),
                'required' => 0
            ),

            array(
                'col' => 'client_swift',
                'name' => lang('swift_code'),
                'required' => 0
            ),

            array(
                'col' => 'client_iban',
                'name' => lang('iban_code'),
                'required' => 0
            )
        );

        return $cols;

    }

    public function db_array()

    {

        $db_array = parent::db_array();

        if (!isset($db_array['client_active']))
        {

            $db_array['client_active'] = 0;

        }

        return $db_array;

    }

    public function delete($id)

    {

        parent::delete($id);


        delete_orphans();

    }

    /**

     * Returns client_id of existing or new record

     */

    public function client_lookup($client_name)

    {

        $client = $this->Mdl_clients->where('client_name', $client_name)->get();

        if ($client->num_rows())
        {

            $client_id = $client->row()->client_id;

        }
        else
        {

            $db_array = array(

                'client_name' => $client_name,

                'company_id' => $this->session->userdata('company_id')
            );

            $client_id = parent::save(NULL, $db_array);

        }

        return $client_id;

    }

    public function with_total()

    {

        $this->filter_select("IFNULL((SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.client_id = ip_clients.client_id)), 0) AS client_invoice_total", FALSE);

        return $this;

    }

    public function with_total_paid()

    {

        $this->filter_select("IFNULL((SELECT SUM(invoice_paid) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.client_id = ip_clients.client_id)), 0) AS client_invoice_paid", FALSE);

        return $this;

    }

    public function with_total_balance()

    {

        $this->filter_select("IFNULL((SELECT SUM(invoice_balance) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.client_id = ip_clients.client_id)), 0) AS client_invoice_balance", FALSE);

        return $this;

    }

    public function is_active()

    {

        $this->filter_where('client_active', 1);

        return $this;

    }

    public function is_inactive()

    {

        $this->filter_where('client_active', 0);

        return $this;

    }

    public function get_by_id($id = NULL)
    {

        $check = $this->db->get_where('ip_clients', array('client_id' => $id))->row();

        return $check;

    }

}
