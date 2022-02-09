<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_suppliers extends Response_Model
{
    public $table = 'ip_suppliers';
    public $primary_key = 'ip_suppliers.supplier_id';
    public $date_created_field = 'supplier_date_created';
    public $date_modified_field = 'supplier_date_modified';

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_supplier_custom.*, ip_suppliers.*', false);
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_supplier_custom', 'ip_supplier_custom.supplier_id = ip_suppliers.supplier_id', 'left');
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_suppliers.supplier_name');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'supplier_name'       => [
                'field' => 'supplier_name',
                'label' => lang('supplier_name'),
                'rules' => 'required'
            ],
            'supplier_reg_number' => [
                'field' => lang("supplier_reg_number"),
                'rules' => 'required|is_unique[ip_suppliers.supplier_reg_number]'
            ],
            'company_id'          => ['field' => 'supplier_name'],
            'supplier_active'     => ['field' => 'supplier_active'],
            'supplier_address_1'  => ['field' => 'supplier_address_1'],
            'supplier_address_2'  => ['field' => 'supplier_address_2'],
            'supplier_city'       => ['field' => 'supplier_city'],
            'supplier_state'      => ['field' => 'supplier_state'],
            'supplier_zip'        => ['field' => 'supplier_zip'],
            'supplier_country'    => ['field' => 'supplier_country'],
            'supplier_phone'      => ['field' => 'supplier_phone'],
            'supplier_fax'        => ['field' => 'supplier_fax'],
            'supplier_mobile'     => ['field' => 'supplier_mobile'],
            'supplier_email'      => ['field' => 'supplier_email'],
            'supplier_web'        => ['field' => 'supplier_web'],
            'supplier_vat_id'     => ['field' => 'user_vat_id'],
            'supplier_tax_code'   => ['field' => 'user_tax_code'],
            'supplier_swift'      => [
                'field' => 'supplier_swift',
                'label' => 'SWIFT',
                'rules' => 'Mdl_suppliers.callback_supplier_swift'
            ],

            'supplier_iban' => [
                'field' => 'supplier_iban',
                'label' => 'IBAN',
                'rules' => 'Mdl_suppliers.callback_supplier_iban'
            ]
        ];
    }

    /**
     * Supplier iban
     * @param $swift
     * @return bool
     */
    function supplier_iban($swift)
    {
        if ($swift == '') {
            return true;
        }

        if (!verify_iban($swift, $machine_format_only = false)) {
            $this->form_validation->set_message('supplier_iban', 'IBAN code is not valid', 'Hello World !');

            return false;
        }
    }

    /**
     * Supplier swift
     * @param $swift
     * @return bool
     */
    function supplier_swift($swift)
    {
        if ($swift == '') {
            return true;
        }

        $regexp = '/^([a-zA-Z]){4}([a-zA-Z]){2}([0-9a-zA-Z]){2}([0-9a-zA-Z]{3})?$/';

        if (!preg_match($regexp, $swift)) {
            $this->form_validation->set_message('supplier_swift', 'SWIFT code is not valid', 'Hello World !');

            return false;
        } else {
            return true;
        }
    }

    /**
     * Get cols name for import
     * @return array
     */
    public function get_cols_name_for_import()
    {
        return [
            ['col' => 'supplier_name', 'name' => lang('supplier_name'), 'required' => 1],
            ['col' => 'supplier_reg_number', 'name' => lang('supplier_reg_number'), 'required' => 1],
            ['col' => 'supplier_address_1', 'name' => lang('street_address'), 'required' => 0],
            ['col' => 'supplier_address_2', 'name' => lang('street_address_2'), 'required' => 0],
            ['col' => 'supplier_city', 'name' => lang('city'), 'required' => 0],
            ['col' => 'supplier_state', 'name' => lang('state'), 'required' => 0],
            ['col' => 'supplier_zip', 'name' => lang('zip_code'), 'required' => 0],
            ['col' => 'supplier_country', 'name' => lang('country'), 'required' => 0],
            ['col' => 'supplier_phone', 'name' => lang('phone_number'), 'required' => 0],
            ['col' => 'supplier_fax', 'name' => lang('fax_number'), 'required' => 0],
            ['col' => 'supplier_mobile', 'name' => lang('mobile_number'), 'required' => 0],
            ['col' => 'supplier_email', 'name' => lang('email_address'), 'required' => 1],
            ['col' => 'supplier_web', 'name' => lang('web_address'), 'required' => 0],
            ['col' => 'supplier_vat_id', 'name' => lang('vat_id'), 'required' => 0],
            ['col' => 'supplier_tax_code', 'name' => lang('tax_code'), 'required' => 0],
            ['col' => 'supplier_swift', 'name' => lang('swift_code'), 'required' => 0],
            ['col' => 'supplier_iban', 'name' => lang('iban_code'), 'required' => 0]
        ];
    }

    /**
     * DB array
     * @return array
     */
    public function db_array()
    {
        $data = parent::db_array();

        if (!isset($data['supplier_active'])) {
            $data['supplier_active'] = 0;
        }

        return $data;
    }

    /**
     * Delete
     * @param $id
     */
    public function delete($id)
    {
        parent::delete($id);
        delete_orphans();
    }

    /**
     * Returns supplier_id of existing or new record
     * @param $supplierName
     * @return null
     */
    public function supplier_lookup($supplierName)
    {
        $companyId = $this->session->userdata('company_id');
        $supplier = $this->Mdl_suppliers
            ->where('supplier_name', $supplierName)
            ->where('company_id', $companyId)
            ->get();

        if ($supplier->num_rows()) {
            $supplierId = $supplier->row()->supplier_id;
        } else {
            $data = [
                'supplier_name' => $supplierName,
                'company_id'    => $companyId
            ];

            $supplierId = parent::save(null, $data);
        }

        return $supplierId;
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
                        WHERE ip_invoices.supplier_id = ip_suppliers.supplier_id
                    )
                ), 0
            )AS supplier_invoice_total",
            false);

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
                        WHERE ip_invoices.supplier_id = ip_suppliers.supplier_id
                    )
                ), 0
            ) AS supplier_invoice_paid",
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
                        WHERE ip_invoices.supplier_id = ip_suppliers.supplier_id
                    )
                ), 0
            ) AS supplier_invoice_balance",
            false);

        return $this;
    }

    /**
     * Is active
     * @return $this
     */
    public function is_active()
    {
        $this->filter_where('supplier_active', 1);

        return $this;
    }

    /**
     * Is inactive
     * @return $this
     */
    public function is_inactive()
    {
        $this->filter_where('supplier_active', 0);

        return $this;
    }
}
