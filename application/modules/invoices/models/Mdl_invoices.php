<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_invoices extends Response_Model
{
    const STATUS_DRAFT = 1;
    const STATUS_SENT = 2;
    const STATUS_VIEWED = 3;
    const STATUS_PAID = 4;

    public $table = 'ip_invoices';
    public $primary_key = 'ip_invoices.invoice_id';
    public $date_modified_field = 'invoice_date_modified';

    /**
     * Mdl_invoices constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_clients',
            'Mdl_companies',
            'Mdl_invoice_groups',
            'invoices/Mdl_items',
            'suppliers/Mdl_suppliers',
        ]);
    }

    /**
     * Statuses
     * @return array
     */
    public function statuses()
    {
        return [
            self::STATUS_DRAFT  => [
                'label' => lang('draft'),
                'class' => 'draft',
                'href'  => 'invoices/status/draft'
            ],
            self::STATUS_SENT   => [
                'label' => lang('sent'),
                'class' => 'sent',
                'href'  => 'invoices/status/sent'
            ],
            self::STATUS_VIEWED => [
                'label' => lang('viewed'),
                'class' => 'viewed',
                'href'  => 'invoices/status/viewed'
            ],
            self::STATUS_PAID   => [
                'label' => lang('paid'),
                'class' => 'paid',
                'href'  => 'invoices/status/paid'
            ]
        ];
    }

    /**
     * Default select
     */
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
            ip_clients.*,
            ip_companies.*,
            ip_suppliers.*,
            ip_invoice_amounts.invoice_amount_id,
            s1.setting_value as invoice_logo,
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

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_invoices.invoice_id DESC');
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_invoices.client_id', 'left');
        $this->db->join('ip_users', 'ip_users.user_id = ip_invoices.user_id', 'left');
        $this->db->join('ip_companies', 'ip_companies.company_id = ip_invoices.company_id', 'left');
        $this->db->join('ip_invoice_amounts', 'ip_invoice_amounts.invoice_id = ip_invoices.invoice_id', 'left');
        $this->db->join('ip_suppliers', 'ip_suppliers.supplier_id = ip_invoices.supplier_id', 'left');
        $this->db->join('ip_settings s1', '
            ip_invoices.company_id = s1.company_id AND
            s1.setting_key = "invoice_logo"',
            'left'
        );
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'client_name'          => [
                'field' => 'client_name',
                'label' => lang('client'),
                'rules' => 'required'
            ],
            'client_id'            => [
                'field' => 'client_name',
                'label' => lang('client'),
                'rules' => 'required'
            ],
            'client_reg_number'    => [
                'field' => 'client_reg_number',
                'label' => lang('client_reg_number'),
                'rules' => 'required'
            ],
            'client_address_1'     => [
                'field' => 'client_address_1',
                'label' => lang('client_address_1'),
                'rules' => 'required'
            ],
            'client_vat_id'        => [
                'field' => 'client_vat_id',
                'label' => lang('client_vat_id')
            ],
            'company_id'           => [
                'field' => 'company_id'
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

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules_save_invoice()
    {
        return [
            'invoice_number'       => [
                'field' => 'invoice_number',
                'label' => lang('invoice') . ' #',
                'rules' => 'callback_invoice_number_check'
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

    public function create($array = null, $includeInvoiceTaxRates = true)
    {
        $invoiceGroupId = $this->input->post('invoice_group_id', true);
        $invoiceNumber = $this->Mdl_invoice_groups->generateInvoiceNumber($invoiceGroupId);
        $invoiceUrlKey = random_string('alnum', 15);
        $create_date = strtotime($this->input->post('invoice_date_created', true));
        $due_date = $create_date + 2592000;

        $data = [
            'client_id'             => $this->input->post('client_id', true),
            'invoice_date_created'  => date_to_mysql($this->input->post('invoice_date_created', true)),
            'invoice_group_id'      => $this->input->post('invoice_group_id', true),
            'invoice_time_created'  => $this->input->post('invoice_time_created', true),
            'invoice_password'      => $this->input->post('invoice_password', true),
            'user_id'               => $this->input->post('user_id', true),
            'payment_method'        => $this->input->post('payment_method', true),
            'company_id'            => $this->input->post('company_id', true),
            'invoice_url_key'       => $invoiceUrlKey,
            'invoice_date_modified' => date('Y-m-d', time()) . ' ' . $this->input->post('invoice_time_created', true),
            'invoice_date_due'      => date('Y-m-d', $due_date),
            'invoice_number'        => $invoiceNumber,
            'supplier_id'           => 0,
            'time'                  => '00:00:00'
        ];

        $this->db->insert('ip_invoices', $data);
        $invoiceId = $this->db->insert_id();
        $this->db->insert('ip_invoice_amounts', ['invoice_id' => $invoiceId]);

        if ($includeInvoiceTaxRates) {
            if ($this->Mdl_settings->setting('default_invoice_tax_rate')) {
                $this->db->insert('ip_invoice_tax_rates', [
                    'invoice_id'              => $invoiceId,
                    'tax_rate_id'             => $this->Mdl_settings->setting('default_invoice_tax_rate'),
                    'include_item_tax'        => $this->Mdl_settings->setting('default_include_item_tax'),
                    'invoice_tax_rate_amount' => 0
                ]);
            }
        }

        return $invoiceId;
    }

    public function create_suppliers($supplierData = null, $includeInvoiceTaxRates = true)
    {
        $invoiceId = parent::save(null, $supplierData);

        // Create an invoice amount record
        $supplierData = [
            'invoice_id' => $invoiceId
        ];

        $this->db->insert('ip_invoice_amounts', $supplierData);

        if ($includeInvoiceTaxRates) {
            if ($this->Mdl_settings->setting('default_invoice_tax_rate')) {
                $this->db->insert('ip_invoice_tax_rates', [
                    'invoice_id'              => $invoiceId,
                    'tax_rate_id'             => $this->Mdl_settings->setting('default_invoice_tax_rate'),
                    'include_item_tax'        => $this->Mdl_settings->setting('default_include_item_tax'),
                    'invoice_tax_rate_amount' => 0
                ]);
            }
        }

        return $invoiceId;
    }

    /**
     * Copies invoice items, tax rates, etc from source to target
     * @param int $sourceId
     * @param int $targetId
     */
    public function copy_invoice($sourceId, $targetId)
    {
        $invoiceItems = $this->Mdl_items->where('invoice_id', $sourceId)->get()->result();

        foreach ($invoiceItems as $item) {
            $this->Mdl_items->save($targetId, null, [
                'invoice_id'       => $targetId,
                'item_tax_rate_id' => $item->item_tax_rate_id,
                'item_name'        => $item->item_name,
                'item_description' => $item->item_description,
                'item_quantity'    => $item->item_quantity,
                'item_price'       => $item->item_price,
                'item_order'       => $item->item_order
            ]);
        }

        $invoiceTaxRates = $this->Mdl_invoice_tax_rates->where('invoice_id', $sourceId)->get()->result();

        foreach ($invoiceTaxRates as $taxRate) {
            $this->Mdl_invoice_tax_rates->save($targetId, null, [
                'invoice_id'              => $targetId,
                'tax_rate_id'             => $taxRate->tax_rate_id,
                'include_item_tax'        => $taxRate->include_item_tax,
                'invoice_tax_rate_amount' => $taxRate->invoice_tax_rate_amount
            ]);
        }
    }

    /**
     * Copies invoice items, tax rates, etc from source to target
     * @param int $sourceId
     * @param int $targetId
     */
    public function copy_credit_invoice($sourceId, $targetId)
    {
        $invoiceItems = $this->Mdl_items->where('invoice_id', $sourceId)->get()->result();

        foreach ($invoiceItems as $item) {
            $this->Mdl_items->save($targetId, null, [
                'invoice_id'       => $targetId,
                'item_tax_rate_id' => $item->item_tax_rate_id,
                'item_name'        => $item->item_name,
                'item_description' => $item->item_description,
                'item_quantity'    => -$item->item_quantity,
                'item_price'       => $item->item_price,
                'item_order'       => $item->item_order
            ]);
        }

        $invoiceTaxRates = $this->Mdl_invoice_tax_rates->where('invoice_id', $sourceId)->get()->result();

        foreach ($invoiceTaxRates as $taxRate) {
            $this->Mdl_invoice_tax_rates->save($targetId, null, [
                'invoice_id'              => $targetId,
                'tax_rate_id'             => $taxRate->tax_rate_id,
                'include_item_tax'        => $taxRate->include_item_tax,
                'invoice_tax_rate_amount' => -$taxRate->invoice_tax_rate_amount
            ]);
        }
    }

    public function db_array()
    {
        $data = parent::db_array();

        if (
            (!isset($data['client_id'])) ||
            (isset($data['client_id']) && $data['client_id'] == -1)
        ) {
            $clientId = $this->Mdl_clients->save(null, [
                'client_name'       => $data['client_name'],
                'company_id'        => $this->session->userdata('company_id'),
                'client_reg_number' => $data['client_reg_number'],
                'client_address_1'  => $data['client_address_1'],
                'client_vat_id'     => $data['client_vat_id']
            ]);

            $data['client_id'] = $clientId;
        }

        unset($data['client_reg_number']);
        unset($data['client_address_1']);
        unset($data['client_vat_id']);
        unset($data['client_vat_id']);
        unset($data['client_name']);

        $data['invoice_date_created'] = date_to_mysql($data['invoice_date_created']);
        $data['invoice_date_due'] = $this->get_date_due($data['invoice_date_created']);
        $data['invoice_number'] = $this->get_invoice_number($data['invoice_group_id']);
        $data['invoice_terms'] = $this->Mdl_settings->setting('default_invoice_terms');

        if (!isset($data['invoice_status_id'])) {
            $data['invoice_status_id'] = self::STATUS_DRAFT;
        }

        $data['invoice_url_key'] = $this->get_url_key();

        return $data;
    }

    /**
     * Get date due
     * @param $invoiceDateCreated
     * @return string
     */
    public function get_date_due($invoiceDateCreated)
    {
        $invoiceDateDue = new DateTime($invoiceDateCreated);
        $invoiceDateDue->add(new DateInterval('P' . $this->Mdl_settings->setting('invoices_due_after') . 'D'));

        return $invoiceDateDue->format('Y-m-d');
    }

    /**
     * Get invoice number
     * @param $invoiceGroupId
     * @return mixed
     */
    public function get_invoice_number($invoiceGroupId)
    {
        return $this->Mdl_invoice_groups->generateInvoiceNumber($invoiceGroupId);
    }

    /**
     * Get url key
     * @return string
     */
    public function get_url_key()
    {
        return random_string('alnum', 15);
    }

    /**
     * TODO: remove this
     * @param $invoiceId
     * @param bool $setFlash
     */
    public function delete($invoiceId, $setFlash = true)
    {
        parent::delete($invoiceId, $setFlash);

        delete_orphans();
    }

    /**
     * Is open
     * @return $this
     */
    public function is_open()
    {
        $this->filter_where_in('invoice_status_id', [self::STATUS_SENT, self::STATUS_VIEWED]);

        return $this;
    }

    /**
     * Guest visible
     * @return $this
     */
    public function guest_visible()
    {
        $this->filter_where_in('invoice_status_id', [
            self::STATUS_SENT,
            self::STATUS_VIEWED,
            self::STATUS_PAID
        ]);

        return $this;
    }

    /**
     * Is draft
     * @return $this
     */
    public function is_draft()
    {
        $this->filter_where('invoice_status_id', self::STATUS_DRAFT);

        return $this;
    }

    /**
     * Is sent
     * @return $this
     */
    public function is_sent()
    {
        $this->filter_where('invoice_status_id', self::STATUS_SENT);

        return $this;
    }

    /**
     * Is viewed
     * @return $this
     */
    public function is_viewed()
    {
        $this->filter_where('invoice_status_id', self::STATUS_VIEWED);

        return $this;
    }

    /**
     * Is paid
     * @return $this
     */
    public function is_paid()
    {
        $this->filter_where('invoice_status_id', self::STATUS_PAID);

        return $this;
    }

    /**
     * Is overdue
     * @return $this
     */
    public function is_overdue()
    {
        $this->filter_having('is_overdue', 1);

        return $this;
    }

    /**
     * Check if received
     * @param $id
     * @return bool
     */
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

    /**
     * Is received
     * @return $this
     */
    public function is_received()
    {
        $regNumber = $this->Mdl_companies
            ->where('company_id', $this->session->userdata('company_id'))
            ->get()
            ->result()[0]->company_code;

        $meAsClient = $this->Mdl_clients->where('client_reg_number', $regNumber)->get()->result();

        $clientsArray = [];
        $clientsArray[] = -1;
        foreach ($meAsClient as $client) {
            $clientsArray[] = $client->client_id;
        }

        $this->where('
            (
                (
                    ip_invoices.client_id IN (' . implode(",", $clientsArray) . ') AND
                    ip_invoices.is_read_only=1 AND
                    ip_invoices.invoice_status_id=2 AND
                    ip_invoices.is_received=0
                )
                OR
                (
                    ip_invoices.is_received=1
                    AND ip_invoices.company_id =' . $this->session->userdata("company_id") . '
                )
            )', null, false);

        return $this;
    }

    /**
     * By client
     * @param $clientId
     * @return $this
     */
    public function by_client($clientId)
    {
        $this->filter_where('ip_invoices.client_id', $clientId);

        return $this;
    }

    /**
     * By supplier
     * @param $supplierId
     * @return $this
     */
    public function by_supplier($supplierId)
    {
        $this->filter_where('ip_invoices.supplier_id', $supplierId);

        return $this;
    }

    /**
     * Mark viewed
     * @param $invoiceId
     */
    public function mark_viewed($invoiceId)
    {
        $this->db->select('invoice_status_id');
        $this->db->where('invoice_id', $invoiceId);

        $invoice = $this->db->get('ip_invoices');

        if ($invoice->num_rows()) {
            if ($invoice->row()->invoice_status_id == self::STATUS_SENT) {
                $this->db->where('invoice_id', $invoiceId);
                $this->db->update('ip_invoices', ['invoice_status_id' => self::STATUS_VIEWED]);
            }

            if (
                $this->config->item('disable_read_only') == null &&
                $this->Mdl_settings->setting('read_only_toggle') == 'viewed'
            ) {
                $this->db->where('invoice_id', $invoiceId);
                $this->db->update('ip_invoices', ['is_read_only' => 1]);
            }
        }
    }

    /**
     * Mark sent
     * @param $invoiceId
     */
    public function mark_sent($invoiceId)
    {
        $this->db->select('invoice_status_id');
        $this->db->where('invoice_id', $invoiceId);
        $invoice = $this->db->get('ip_invoices');

        if ($invoice->num_rows()) {
            if ($invoice->row()->invoice_status_id == self::STATUS_DRAFT) {
                $this->db->where('invoice_id', $invoiceId);
                $this->db->update('ip_invoices', ['invoice_status_id' => self::STATUS_SENT]);
            }

            if (
                $this->config->item('disable_read_only') == null &&
                $this->Mdl_settings->setting('read_only_toggle') == 'sent'
            ) {
                $this->db->where('invoice_id', $invoiceId);
                $this->db->update('ip_invoices', ['is_read_only' => 1]);
            }
        }
    }

    /**
     * Mark as rec seen
     * @param $invoiceId
     */
    public function mark_as_rec_seen($invoiceId)
    {
        $this->db->where('invoice_id', $invoiceId);
        $this->db->update('ip_invoices', ['is_rec_and_seen' => 1]);
    }

    /**
     * Invoice number check
     * @param $invoiceNumber
     * @return bool
     */
    public function invoice_number_check($invoiceNumber)
    {
        if ($this->id) {
            $old_invoice_number = $this->Mdl_invoices->getByPk($this->id)->invoice_number;
            if ($old_invoice_number != $invoiceNumber) {
                $this->db->where('invoice_number', $invoiceNumber);
                $this->db->where('company_id', $this->session->userdata('company_id'));
                $invoice = $this->db->get('ip_invoices');

                if ($invoice->num_rows()) {
                    $this->form_validation->set_message('invoice_number_check', 'Invoice number must be unique');

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get cols name for export
     * @return array
     */
    public function get_cols_name_for_export()
    {
        $symbol = $this->Mdl_settings->setting('currency_symbol');
        $cols = [
            ['col' => 'invoice_date_created', 'name' => lang('date'), 'required' => 1, 'type' => 'Invoice'],
            ['col' => 'invoice_number', 'name' => lang('invoice'), 'required' => 1, 'type' => 'Invoice'],
            ['col' => 'invoice_status_id', 'name' => lang('status'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'client_name', 'name' => lang('client_name'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'client_email', 'name' => lang('email_address'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'invoice_date_created', 'name' => lang('date'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'invoice_date_due', 'name' => lang('due_date'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'invoice_item_subtotal', 'name' => lang('subtotal'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'invoice_item_tax_total', 'name' => lang('item_tax'), 'required' => 0, 'type' => 'Invoice'],
            ['col' => 'invoice_item_tax_total', 'name' => lang('invoice_tax'), 'required' => 0, 'type' => 'Invoice'],
            [
                'col'      => 'invoice_discount_amount',
                'name'     => lang('discount') . $symbol,
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
                'name'     => lang('item_discount') . " - $symbol",
                'required' => 0,
                'type'     => 'Item'
            ],
            ['col' => 'item_total', 'name' => lang('total'), 'required' => 0, 'type' => 'Item']
        ];

        return $cols;
    }

    /**
     * TODO: rename to findByPk
     * Get invoice by id
     * @param $id
     * @return mixed
     */
    public function get_invoice_by_id($id)
    {
        $query = $this->db->query("
            SELECT *
            FROM ip_invoices
                INNER JOIN ip_invoice_amounts
                    ON ip_invoices.invoice_id = ip_invoice_amounts.invoice_id
            WHERE ip_invoices.invoice_id = ?
        ", $id);

        return $query->result_array();
    }
}
