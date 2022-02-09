<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_clients extends Response_Model
{
    public $table = 'ip_clients';
    public $primary_key = 'ip_clients.client_id';
    public $date_created_field = 'client_date_created';
    public $date_modified_field = 'client_date_modified';

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_client_custom.*, ip_clients.*', false);
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_client_custom', 'ip_client_custom.client_id = ip_clients.client_id', 'left');
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_clients.client_name');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'client_name'       => [
                'field' => 'client_name',
                'label' => lang('client_name'),
                'rules' => 'required'
            ],
            'client_reg_number' => [
                'field' => 'client_reg_number',
                'label' => lang('client_reg_number'),
                'rules' => 'required|callback_Mdl_clients.validate_reg_number'
            ],
            'company_id'        => ['field' => 'client_name'],
            'client_email'      => ['field' => 'client_email'],
            'client_active'     => ['field' => 'client_active'],
            'client_address_1'  => ['field' => 'client_address_1'],
            'client_address_2'  => ['field' => 'client_address_2'],
            'client_city'       => ['field' => 'client_city'],
            'client_state'      => ['field' => 'client_state'],
            'client_zip'        => ['field' => 'client_zip'],
            'client_country'    => ['field' => 'client_country'],
            'client_phone'      => ['field' => 'client_phone'],
            'client_fax'        => ['field' => 'client_fax'],
            'client_mobile'     => ['field' => 'client_mobile'],
            'client_web'        => ['field' => 'client_web'],
            'client_vat_id'     => ['field' => 'user_vat_id'],
            'client_tax_code'   => ['field' => 'user_tax_code'],
            'client_swift'      => [
                'field' => 'client_swift',
                'label' => 'SWIFT',
                'rules' => 'callback_Mdl_clients.form_client_swift'
            ],
            'client_iban'       => [
                'field' => 'client_iban',
                'label' => 'IBAN',
                'rules' => 'callback_Mdl_clients.form_client_iban'
            ],
        ];
    }

    /**
     * Callback form function
     * @param $swift
     * @return bool
     */
    public function form_client_iban($swift)
    {
        if ($swift != '' && !verify_iban($swift, $machine_format_only = false)) {
            $this->form_validation->set_message('client_iban', 'IBAN code is not valid', 'Hello World !');

            return false;
        }

        return true;
    }

    /**
     * Validate registration number
     * @param $number
     * @return bool
     */
    public function validate_reg_number($number)
    {
        $companyId = $this->input->post('company_id', true);
        $userId = $this->input->post('client_id', true);

        $clients = $this->db
            ->where('company_id', $companyId)
            ->where('client_reg_number', $number)
            ->from('ip_clients')
            ->get()
            ->result();

        if (count($clients) > 0) {
            foreach ($clients as $c) {
                if($c->client_id != $userId) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Callback form function
     * @param $swift
     * @return bool
     */
    public function form_client_swift($swift)
    {
        $regexp = '/^([a-zA-Z]){4}([a-zA-Z]){2}([0-9a-zA-Z]){2}([0-9a-zA-Z]{3})?$/';
        if ($swift != '' && !preg_match($regexp, $swift)) {
            $this->form_validation->set_message('client_swift', 'SWIFT code is not valid', 'Hello World !');

            return false;
        }

        return true;
    }

    /**
     * Get column names for import
     * @return array
     */
    public function get_cols_name_for_import()
    {
        return [
            ['col' => 'client_name', 'name' => lang('client_name'), 'required' => 1],
            ['col' => 'client_reg_number', 'name' => lang('client_reg_number'), 'required' => 1],
            ['col' => 'client_address_1', 'name' => lang('street_address'), 'required' => 1],
            ['col' => 'client_email', 'name' => lang('email_address'), 'required' => 1],
            ['col' => 'client_address_2', 'name' => lang('street_address_2'), 'required' => 0],
            ['col' => 'client_city', 'name' => lang('city'), 'required' => 0],
            ['col' => 'client_state', 'name' => lang('state'), 'required' => 0],
            ['col' => 'client_zip', 'name' => lang('zip_code'), 'required' => 0],
            ['col' => 'client_country', 'name' => lang('country'), 'required' => 0],
            ['col' => 'client_phone', 'name' => lang('phone_number'), 'required' => 0],
            ['col' => 'client_fax', 'name' => lang('fax_number'), 'required' => 0],
            ['col' => 'client_mobile', 'name' => lang('mobile_number'), 'required' => 0],
            ['col' => 'client_web', 'name' => lang('web_address'), 'required' => 0],
            ['col' => 'client_vat_id', 'name' => lang('vat_id'), 'required' => 0],
            ['col' => 'client_tax_code', 'name' => lang('tax_code'), 'required' => 0],
            ['col' => 'client_swift', 'name' => lang('swift_code'), 'required' => 0],
            ['col' => 'client_iban', 'name' => lang('iban_code'), 'required' => 0]
        ];
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        if (!isset($db_array['client_active'])) {
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
    public function client_lookup($name)
    {
        $client = $this->Mdl_clients->where('client_name', $name)->get();

        if ($client->num_rows()) {
            $client_id = $client->row()->client_id;
        } else {
            $data = [
                'client_name' => $name,
                'company_id'  => $this->session->userdata('company_id')
            ];
            $client_id = parent::save(null, $data);
        }

        return $client_id;
    }

    /**
     * With total
     * @return $this
     */
    public function with_total()
    {
        $this->filter_select("
            IFNULL(
                (
                    SELECT SUM(invoice_total)
                    FROM ip_invoice_amounts
                    WHERE invoice_id IN (
                        SELECT invoice_id
                        FROM ip_invoices
                        WHERE ip_invoices.client_id = ip_clients.client_id
                    )
                )
            , 0) AS client_invoice_total",
            false
        );

        return $this;
    }

    /**
     * With total paid
     * @return $this
     */
    public function with_total_paid()
    {
        $this->filter_select("
            IFNULL(
                (
                    SELECT SUM(invoice_paid)
                    FROM ip_invoice_amounts
                    WHERE invoice_id IN (
                        SELECT invoice_id
                        FROM ip_invoices
                        WHERE ip_invoices.client_id = ip_clients.client_id
                    )
                )
            , 0) AS client_invoice_paid",
            false);

        return $this;
    }

    /**
     * With total balance
     * @return $this
     */
    public function with_total_balance()
    {
        $this->filter_select("
            IFNULL(
                (
                    SELECT SUM(invoice_balance)
                    FROM ip_invoice_amounts
                    WHERE invoice_id IN (
                        SELECT invoice_id
                        FROM ip_invoices
                        WHERE ip_invoices.client_id = ip_clients.client_id
                    )
                )
            , 0) AS client_invoice_balance",
            false);

        return $this;
    }

    /**
     * Is active
     * @return $this
     */
    public function is_active()
    {
        $this->filter_where('client_active', 1);

        return $this;
    }

    /**
     * Is inactive
     * @return $this
     */
    public function is_inactive()
    {
        $this->filter_where('client_active', 0);

        return $this;
    }
}
