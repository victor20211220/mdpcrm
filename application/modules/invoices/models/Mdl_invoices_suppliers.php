<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_invoices_suppliers extends Response_Model
{
    public $table = 'ip_invoices';
    public $primary_key = 'ip_invoices.invoice_id';
    public $date_modified_field = 'invoice_date_modified';

    public function statuses()
    {
        return [
            '1' => [
                'label' => lang('draft'),
                'class' => 'draft',
                'href'  => 'invoices/status/draft'
            ],
            '2' => [
                'label' => lang('sent'),
                'class' => 'sent',
                'href'  => 'invoices/status/sent'
            ],
            '3' => [
                'label' => lang('viewed'),
                'class' => 'viewed',
                'href'  => 'invoices/status/viewed'
            ],
            '4' => [
                'label' => lang('paid'),
                'class' => 'paid',
                'href'  => 'invoices/status/paid'
            ]
        ];
    }

    public function default_select()
    {
        $this->db->select("
            SQL_CALC_FOUND_ROWS
            ip_users.user_name,
            ip_users.user_company,
            ip_users.user_address_1,
            ip_users.user_address_2,
            ip_users.user_city,
            ip_users.user_state,
            ip_users.user_zip,
            ip_users.user_country,
            ip_users.user_phone,
            ip_users.user_fax,
            ip_users.user_mobile,
            ip_users.user_email,
            ip_users.user_web,
            ip_users.user_vat_id,
            ip_users.user_tax_code,
            ip_suppliers.*,
            ip_companies.*,
            ip_invoice_amounts.invoice_amount_id,
            IFNULL(ip_invoice_amounts.invoice_item_subtotal, '0.00') AS invoice_item_subtotal,
            IFNULL(ip_invoice_amounts.invoice_item_tax_total, '0.00') AS invoice_item_tax_total,
            IFNULL(ip_invoice_amounts.invoice_tax_total, '0.00') AS invoice_tax_total,
            IFNULL(ip_invoice_amounts.invoice_total, '0.00') AS invoice_total,
            IFNULL(ip_invoice_amounts.invoice_paid, '0.00') AS invoice_paid,
            IFNULL(ip_invoice_amounts.invoice_balance, '0.00') AS invoice_balance,
            ip_invoice_amounts.invoice_sign AS invoice_sign,
            (CASE WHEN ip_invoices.invoice_status_id NOT IN (1,4) AND DATEDIFF(NOW(), invoice_date_due) > 0 THEN 1 ELSE 0 END) is_overdue,
            DATEDIFF(NOW(), invoice_date_due) AS days_overdue,
            (CASE (SELECT COUNT(*) FROM ip_invoices_recurring WHERE ip_invoices_recurring.invoice_id = ip_invoices.invoice_id and ip_invoices_recurring.recur_next_date <> '0000-00-00') WHEN 0 THEN 0 ELSE 1 END) AS invoice_is_recurring,
            ip_invoices.*", false
        );
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_invoices.invoice_id DESC');
    }

    public function default_join()
    {
        $this->db->join('ip_suppliers', 'ip_suppliers.supplier_id = ip_invoices.supplier_id', 'left');
        $this->db->join('ip_users', 'ip_users.user_id = ip_invoices.user_id', 'left');
        $this->db->join('ip_companies', 'ip_companies.company_id = ip_invoices.company_id', 'left');
        $this->db->join('ip_invoice_amounts', 'ip_invoice_amounts.invoice_id = ip_invoices.invoice_id', 'left');
    }

    public function validation_rules()
    {
        return [
            'supplier_name'        => [
                'field' => 'supplier_name',
                'label' => lang('supplier'),
                'rules' => 'required'
            ],
            'supplier_id'          => [
                'field' => 'supplier_name',
                'label' => lang('supplier'),
                'rules' => 'required'
            ],
            'supplier_reg_number'  => [
                'field' => 'supplier_reg_number',
                'label' => lang('supplier_reg_number'),
                'rules' => 'required'
            ],
            'supplier_address_1'   => [
                'field' => 'supplier_address_1',
                'label' => lang('supplier_address_1'),
                'rules' => 'required'
            ],
            'supplier_vat_id'      => [
                'field' => 'supplier_vat_id',
                'label' => lang('supplier_vat_id')
            ],
            'company_id'           => [
                'field' => 'company_id'
            ],
            'invoice_number' => [
                'field' => 'invoice_number'
            ],
            'invoice_date_created' => [
                'field' => 'invoice_date_created',
                'label' => lang('invoice_date'),
                'rules' => 'required'
            ],
            'invoice_time_created' => [
                'rules' => 'required'
            ],
            'invoice_group_id'     => [
                'field' => 'invoice_group_id',
                'label' => lang('invoice_group'),
                'rules' => 'required'
            ],
            'invoice_password'     => [
                'field' => 'invoice_password',
                'label' => lang('invoice_password')
            ],
            'user_id'              => [
                'field' => 'user_id',
                'label' => lang('user'),
                'rule'  => 'required'
            ],
            'payment_method'       => [
                'field' => 'payment_method',
                'label' => lang('payment_method')
            ],
            'is_received'          => [
                'field' => 'is_received'
            ],
        ];
    }

    public function validation_rules_save_invoice()
    {
        return [
            'invoice_number'       => [
                'field' => 'invoice_number',
                'label' => lang('invoice') . ' #'
            ],
            'company_id'           => [
                'field' => 'company_id'
            ],
            'invoice_date_created' => [
                'field' => 'invoice_date_created',
                'label' => lang('date'),
                'rules' => 'required'
            ],
            'invoice_date_due'     => [
                'field' => 'invoice_date_due',
                'label' => lang('due_date'),
                'rules' => 'required'
            ],
            'invoice_time_created' => [
                'rules' => 'required'
            ],
            'invoice_password'     => [
                'field' => 'invoice_password',
                'label' => lang('invoice_password')
            ],
            'is_received'          => [
                'field' => 'is_received'
            ],
        ];
    }

    public function create($db_array = null, $include_invoice_tax_rates = true)
    {
        //$this->Mdl_invoices->set_form_value('company_id', $this->session->userdata('company_id'));
        //$db_array['company_id'] = $this->session->userdata('company_id');

        $invoice_id = parent::save(null, $db_array);

        // Create an invoice amount record
        $db_array = [
            'invoice_id' => $invoice_id
        ];

        $this->db->insert('ip_invoice_amounts', $db_array);

        if ($include_invoice_tax_rates) {
            // Create the default invoice tax record if applicable
            if ($this->Mdl_settings->setting('default_invoice_tax_rate')) {
                $db_array = [
                    'invoice_id'              => $invoice_id,
                    'tax_rate_id'             => $this->Mdl_settings->setting('default_invoice_tax_rate'),
                    'include_item_tax'        => $this->Mdl_settings->setting('default_include_item_tax'),
                    'invoice_tax_rate_amount' => 0
                ];

                $this->db->insert('ip_invoice_tax_rates', $db_array);
            }
        }

        return $invoice_id;
    }

    public function create_suppliers($db_array_supplier = null, $include_invoice_tax_rates = true)
    {
        $invoice_id = parent::save(null, $db_array_supplier);

        // Create an invoice amount record
        $db_array_supplier = [
            'invoice_id' => $invoice_id
        ];

        $this->db->insert('ip_invoice_amounts', $db_array_supplier);

        if ($include_invoice_tax_rates) {
            // Create the default invoice tax record if applicable
            if ($this->Mdl_settings->setting('default_invoice_tax_rate')) {
                $db_array_supplier = [
                    'invoice_id'              => $invoice_id,
                    'tax_rate_id'             => $this->Mdl_settings->setting('default_invoice_tax_rate'),
                    'include_item_tax'        => $this->Mdl_settings->setting('default_include_item_tax'),
                    'invoice_tax_rate_amount' => 0
                ];

                $this->db->insert('ip_invoice_tax_rates', $db_array_supplier);
            }
        }

        return $invoice_id;
    }

    /**
     * Copies invoice items, tax rates, etc from source to target
     * @param int $source_id
     * @param int $target_id
     */
    public function copy_invoice($source_id, $target_id)
    {
        $this->load->model('invoices/Mdl_items');

        $invoice_items = $this->Mdl_items->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_items as $invoice_item) {
            $db_array = [
                'invoice_id'       => $target_id,
                'item_tax_rate_id' => $invoice_item->item_tax_rate_id,
                'item_name'        => $invoice_item->item_name,
                'item_description' => $invoice_item->item_description,
                'item_quantity'    => $invoice_item->item_quantity,
                'item_price'       => $invoice_item->item_price,
                'item_order'       => $invoice_item->item_order
            ];

            $this->Mdl_items->save($target_id, null, $db_array);
        }

        $invoice_tax_rates = $this->Mdl_invoice_tax_rates->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_tax_rates as $invoice_tax_rate) {
            $db_array = [
                'invoice_id'              => $target_id,
                'tax_rate_id'             => $invoice_tax_rate->tax_rate_id,
                'include_item_tax'        => $invoice_tax_rate->include_item_tax,
                'invoice_tax_rate_amount' => $invoice_tax_rate->invoice_tax_rate_amount
            ];

            $this->Mdl_invoice_tax_rates->save($target_id, null, $db_array);
        }
    }

    /**
     * Copies invoice items, tax rates, etc from source to target
     * @param int $source_id
     * @param int $target_id
     */
    public function copy_credit_invoice($source_id, $target_id)
    {
        $this->load->model('invoices/Mdl_items');

        $invoice_items = $this->Mdl_items->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_items as $invoice_item) {
            $db_array = [
                'invoice_id'       => $target_id,
                'item_tax_rate_id' => $invoice_item->item_tax_rate_id,
                'item_name'        => $invoice_item->item_name,
                'item_description' => $invoice_item->item_description,
                'item_quantity'    => -$invoice_item->item_quantity,
                'item_price'       => $invoice_item->item_price,
                'item_order'       => $invoice_item->item_order
            ];

            $this->Mdl_items->save($target_id, null, $db_array);
        }

        $invoice_tax_rates = $this->Mdl_invoice_tax_rates->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_tax_rates as $invoice_tax_rate) {
            $db_array = [
                'invoice_id'              => $target_id,
                'tax_rate_id'             => $invoice_tax_rate->tax_rate_id,
                'include_item_tax'        => $invoice_tax_rate->include_item_tax,
                'invoice_tax_rate_amount' => -$invoice_tax_rate->invoice_tax_rate_amount
            ];

            $this->Mdl_invoice_tax_rates->save($target_id, null, $db_array);
        }
    }

    public function db_array_supplier()
    {
        $db_array_supplier = parent::db_array_supplier();

        // Get the supplier id for the submitted invoice
        $this->load->model('suppliers/Mdl_suppliers');

        if ((!isset($db_array_supplier['supplier_id'])) || (isset($db_array_supplier['supplier_id']) && $db_array_supplier['supplier_id'] == -1)) {
            $db_array_supplier = [
                'supplier_name'       => $db_array_supplier['supplier_name'],
                'company_id'          => $this->session->userdata('company_id'),
                'supplier_reg_number' => $db_array_supplier['supplier_reg_number'],
                'supplier_address_1'  => $db_array_supplier['supplier_address_1'],
                'supplier_vat_id'     => $db_array_supplier['supplier_vat_id']
            ];

            $supplier_id = $this->Mdl_suppliers->save(null, $db_array_supplier);
            $db_array_supplier['supplier_id'] = $supplier_id;

        }

        //$db_array['supplier_id'] = $this->Mdl_suppliers->supplier_lookup($db_array['supplier_name']);

        unset($db_array_supplier['supplier_reg_number']);
        unset($db_array_supplier['supplier_address_1']);
        unset($db_array_supplier['supplier_vat_id']);
        unset($db_array_supplier['suppliert_vat_id']);
        unset($db_array_supplier['supplier_name']);

        $db_array_supplier['invoice_date_created'] = date_to_mysql($db_array_supplier['invoice_date_created']);
        $db_array_supplier['invoice_date_due'] = $this->get_date_due($db_array_supplier['invoice_date_created']);
        $db_array_supplier['invoice_number'] = $this->get_invoice_number($db_array_supplier['invoice_group_id']);
        $db_array_supplier['invoice_terms'] = $this->Mdl_settings->setting('default_invoice_terms');

        if (!isset($db_array_supplier['invoice_status_id'])) {
            $db_array_supplier['invoice_status_id'] = 1;
        }

        // Generate the unique url key
        $db_array_supplier['invoice_url_key'] = $this->get_url_key();

        return $db_array_supplier;
    }

    public function get_date_due($invoice_date_created)
    {
        $invoice_date_due = new DateTime($invoice_date_created);
        $invoice_date_due->add(new DateInterval('P' . $this->Mdl_settings->setting('invoices_due_after') . 'D'));

        return $invoice_date_due->format('Y-m-d');
    }

    public function get_invoice_number($invoice_group_id)
    {
        $this->load->model('Mdl_invoice_groups');

        return $this->Mdl_invoice_groups->generateInvoiceNumber($invoice_group_id);
    }

    public function get_url_key()
    {
        return random_string('alnum', 15);
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        // Get the supplier id for the submitted invoice
        $this->load->model('suppliers/Mdl_suppliers');

        if ((!isset($db_array['supplier_id'])) || (isset($db_array['supplier_id']) && $db_array['supplier_id'] == -1)) {
            $db_array_supplier = [
                'supplier_name'       => $db_array['supplier_name'],
                'company_id'          => $this->session->userdata('company_id'),
                'supplier_reg_number' => $db_array['supplier_reg_number'],
                'supplier_address_1'  => $db_array['supplier_address_1'],
                'supplier_vat_id'     => $db_array['supplier_vat_id']
            ];

            $supplier_id = $this->Mdl_suppliers->save(null, $db_array_supplier);
            $db_array['supplier_id'] = $supplier_id;

        }

        //$db_array['supplier_id'] = $this->Mdl_suppliers->supplier_lookup($db_array['supplier_name']);

        unset($db_array['supplier_reg_number']);
        unset($db_array['supplier_address_1']);
        unset($db_array['supplier_vat_id']);
        unset($db_array['supplier_vat_id']);
        unset($db_array['supplier_name']);

        $db_array['invoice_date_created'] = date_to_mysql($db_array['invoice_date_created']);
        $db_array['invoice_date_due'] = $this->get_date_due($db_array['invoice_date_created']);
        $db_array['invoice_number'] = $this->get_invoice_number($db_array['invoice_group_id']);
        $db_array['invoice_terms'] = $this->Mdl_settings->setting('default_invoice_terms');

        if (!isset($db_array['invoice_status_id'])) {
            $db_array['invoice_status_id'] = 1;
        }

        // Generate the unique url key
        $db_array['invoice_url_key'] = $this->get_url_key();

        return $db_array;
    }

    public function delete($invoice_id)
    {
        parent::delete($invoice_id);

        delete_orphans();
    }

    public function is_open()
    {
        $this->filter_where_in('invoice_status_id', [2, 3]);

        return $this;
    }

    public function guest_visible()
    {
        $this->filter_where_in('invoice_status_id', [2, 3, 4]);

        return $this;
    }

    public function is_draft()
    {
        $this->filter_where('invoice_status_id', 1);

        return $this;
    }

    public function is_sent()
    {
        $this->filter_where('invoice_status_id', 2);

        return $this;
    }

    public function is_viewed()
    {
        $this->filter_where('invoice_status_id', 3);

        return $this;
    }

    public function is_paid()
    {
        $this->filter_where('invoice_status_id', 4);

        return $this;
    }

    public function is_overdue()
    {
        $this->filter_having('is_overdue', 1);

        return $this;
    }

    public function check_if_received($id)
    {
        if (!$id) {
            return false;
        }

        $this->is_received();
        $this->filter_where(' (ip_invoices.invoice_id = ' . $id . ')', null, false);

        $res = $this->Mdl_invoices->get()->result();

        if (count($res) > 0) {
            return true;
        }

        return false;
    }

    public function is_received()
    {

        $this->load->model(
            [
                'suppliers/Mdl_suppliers',
                'Mdl_companies',
            ]
        );

        $reg_number = $this->Mdl_companies->where('company_id',
            $this->session->userdata('company_id'))->get()->result()[0]->company_code;

        $me_as_supplier = $this->Mdl_suppliers->where('supplier_reg_number', $reg_number)->get()->result();

        $suppliers_array = [];
        $suppliers_array[] = -1;
        foreach ($me_as_supplier as $supplier) {
            $suppliers_array[] = $supplier->supplier_id;
        }

        //invoices received from another comnpany in this system - My compoany is a clinet for someone else here - thank's GOD . . . business works
        $this->where(' (
		                 (ip_invoices.supplier_id IN (' . implode(",", $suppliers_array) . ') AND ip_invoices.is_read_only=1 AND ip_invoices.invoice_status_id=2 AND ip_invoices.is_received=0)
		                   OR
						 (ip_invoices.is_received=1 AND ip_invoices.company_id =' . $this->session->userdata("company_id") . ')
					   )

						', null, false);

        return $this;
    }

    public function by_supplier($supplier_id)
    {
        $this->filter_where('ip_invoices.supplier_id', $supplier_id);

        return $this;
    }

    public function mark_viewed($invoice_id)
    {
        $this->db->select('invoice_status_id');
        $this->db->where('invoice_id', $invoice_id);

        $invoice = $this->db->get('ip_invoices');

        if ($invoice->num_rows()) {
            if ($invoice->row()->invoice_status_id == 2) {
                $this->db->where('invoice_id', $invoice_id);
                $this->db->set('invoice_status_id', 3);
                $this->db->update('ip_invoices');
            }

            // Set the invoice to read-only if feature is not disabled and setting is view
            if ($this->config->item('disable_read_only') == null && $this->Mdl_settings->setting('read_only_toggle') == 'viewed') {
                $this->db->where('invoice_id', $invoice_id);
                $this->db->set('is_read_only', 1);
                $this->db->update('ip_invoices');
            }
        }
    }

    public function mark_sent($invoice_id)
    {
        $this->db->select('invoice_status_id');
        $this->db->where('invoice_id', $invoice_id);

        $invoice = $this->db->get('ip_invoices');

        if ($invoice->num_rows()) {
            if ($invoice->row()->invoice_status_id == 1) {
                $this->db->where('invoice_id', $invoice_id);
                $this->db->set('invoice_status_id', 2);
                $this->db->update('ip_invoices');
            }

            // Set the invoice to read-only if feature is not disabled and setting is sent
            if ($this->config->item('disable_read_only') == null && $this->Mdl_settings->setting('read_only_toggle') == 'sent') {
                $this->db->where('invoice_id', $invoice_id);
                $this->db->set('is_read_only', 1);
                $this->db->update('ip_invoices');
            }
        }
    }

    public function mark_as_rec_seen($invoice_id)
    {
        $this->db->where('invoice_id', $invoice_id);
        $this->db->set('is_rec_and_seen', 1);
        $this->db->update('ip_invoices');
    }

    public function invoice_number_check($invoice_number)
    {
        if ($this->id) {
            $old_invoice_number = $this->Mdl_invoices->getByPk($this->id)->invoice_number;
            if ($old_invoice_number != $invoice_number) {
                $this->db->where('invoice_number', $invoice_number);
                $this->db->where('company_id', $this->session->userdata('company_id'));
                $invoice = $this->db->get('ip_invoices');

                if ($invoice->num_rows()) {
                    $this->form_validation->set_message('invoice_number_check', 'Invoice number must be unique');

                    return false;
                } else {

                }
            }

            return true;
        }
    }

    public function get_cols_name_for_export()
    {
        $currency_symbol = $this->Mdl_settings->setting('currency_symbol');
        $cols = [
            ['col' => 'invoice_date_created', 'name' => lang('date'), 'required' => 1, 'type' => 'Invoice'],
            ['col' => 'invoice_number', 'name' => lang('invoice'), 'required' => 1, 'type' => 'Invoice'],
            ['col' => 'invoice_status_id', 'name' => lang('status'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'supplier_name', 'name' => lang('supplier_name'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'supplier_email', 'name' => lang('email_address'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'invoice_date_created', 'name' => lang('date'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'invoice_date_due', 'name' => lang('due_date'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'invoice_item_subtotal', 'name' => lang('subtotal'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'invoice_item_tax_total', 'name' => lang('item_tax'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'invoice_item_tax_total', 'name' => lang('invoice_tax'), 'required' => 0, 'type' => 'Invoice'],
            [
                'col'      => 'invoice_discount_amount',
                'name'     => lang('discount') . $currency_symbol,
                'required' => 0,
                'type'     => 'Invoice'
            ],
            [
                'col'      => 'invoice_discount_percent',
                'name'     => lang('discount') . "%",
                'required' => 0,
                'type'     => 'Invoice'
            ],
            ['col' => 'invoice_total', 'name' => lang('total'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'invoice_paid', 'name' => lang('total_paid'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'invoice_balance', 'name' => lang('balance'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'item_name', 'name' => lang('item'), 'required' => 0, 'type' => 'Item'],
            ['col' => 'item_description', 'name' => lang('description'), 'required' => 0, 'type' => 'Item'],
            ['col' => 'item_quantity', 'name' => lang('quantity'), 'required' => 0, 'type' => 'Item'],
            ['col' => 'item_price', 'name' => lang('price'), 'required' => 0, 'type' => 'Item'],
            ['col' => 'item_tax_rate', 'name' => lang('tax_rate'), 'required' => 0, 'type' => 'Item'],
            ['col' => 'item_tax_total', 'name' => lang('tax'), 'required' => 0, 'type' => 'Item'],
            ['col' => 'item_subtotal', 'name' => lang('subtotal'), 'required' => 0, 'type' => 'Item'],
            [
                'col'      => 'item_discount_amount',
                'name'     => lang('item_discount') . ' - ' . $currency_symbol,
                'required' => 0,
                'type'     => 'Item'
            ],
            ['col' => 'item_total', 'name' => lang('total'), 'required' => 0, 'type' => 'Item']
        ];

        return $cols;
    }
}
