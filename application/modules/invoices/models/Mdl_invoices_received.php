<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_invoices_received extends Response_Model
{
    public $table = 'ip_invoices_received';
    public $primary_key = 'ip_invoices_received.invoice_id';
    public $date_modified_field = 'invoice_date_modified';

    /**
     * Statuses
     * @return array
     */
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
            ip_invoice_amounts.invoice_amount_id,
            IFNULL(ip_invoice_amounts.invoice_item_subtotal, '0.00') AS invoice_item_subtotal,
            IFNULL(ip_invoice_amounts.invoice_item_tax_total, '0.00') AS invoice_item_tax_total,
            IFNULL(ip_invoice_amounts.invoice_tax_total, '0.00') AS invoice_tax_total,
            IFNULL(ip_invoice_amounts.invoice_total, '0.00') AS invoice_total,
            IFNULL(ip_invoice_amounts.invoice_paid, '0.00') AS invoice_paid,
            IFNULL(ip_invoice_amounts.invoice_balance, '0.00') AS invoice_balance,
            ip_invoice_amounts.invoice_sign AS invoice_sign,
            (CASE WHEN ip_invoices_received.invoice_status_id NOT IN (1,4) AND DATEDIFF(NOW(), invoice_date_due) > 0 THEN 1 ELSE 0 END) is_overdue,
            DATEDIFF(NOW(), invoice_date_due) AS days_overdue,
            (CASE (SELECT COUNT(*) FROM ip_invoices_received_recurring WHERE ip_invoices_received_recurring.invoice_id = ip_invoices_received.invoice_id and ip_invoices_received_recurring.recur_next_date <> '0000-00-00') WHEN 0 THEN 0 ELSE 1 END) AS invoice_is_recurring,
            ip_invoices_received.*",
            false
        );
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_invoices_received.invoice_id DESC');
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_invoices_received.client_id');
        $this->db->join('ip_users', 'ip_users.user_id = ip_invoices_received.user_id');
        $this->db->join('ip_companies', 'ip_companies.company_id = ip_invoices_received.company_id');
        $this->db->join('ip_invoice_amounts', 'ip_invoice_amounts.invoice_id = ip_invoices_received.invoice_id',
            'left');
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'client_name' => [
                'field' => 'client_name',
                'label' => lang('client'),
                'rules' => 'required'
            ],

            'client_id' => [
                'field' => 'client_name',
                'label' => lang('client'),
                'rules' => 'required'
            ],

            'client_reg_number' => [
                'field' => 'client_reg_number',
                'label' => lang('client_reg_number'),
                'rules' => 'required'
            ],

            'client_address_1' => [
                'field' => 'client_address_1',
                'label' => lang('client_address_1'),
                'rules' => 'required'
            ],

            'client_vat_id' => [
                'field' => 'client_vat_id',
                'label' => lang('client_vat_id')
            ],

            'company_id' => [
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

            'invoice_group_id' => [
                'field' => 'invoice_group_id',
                'label' => lang('invoice_group'),
                'rules' => 'required'
            ],

            'invoice_password' => [
                'field' => 'invoice_password',
                'label' => lang('invoice_password')
            ],

            'user_id' => [
                'field' => 'user_id',
                'label' => lang('user'),
                'rule'  => 'required'
            ],

            'payment_method' => [
                'field' => 'payment_method',
                'label' => lang('payment_method')
            ],
        ];
    }

    /**
     * Validation rules save invoice
     * @return array
     */
    public function validation_rules_save_invoice()
    {
        return [
            'invoice_number' => [
                'field' => 'invoice_number',
                'label' => lang('invoice') . ' #',
                'rules' => 'callback_invoice_number_check'
            ],

            'company_id' => [
                'field' => 'company_id'
            ],

            'invoice_date_created' => [
                'field' => 'invoice_date_created',
                'label' => lang('date'),
                'rules' => 'required'
            ],

            'invoice_date_due' => [
                'field' => 'invoice_date_due',
                'label' => lang('due_date'),
                'rules' => 'required'
            ],

            'invoice_time_created' => [
                'rules' => 'required'
            ],

            'invoice_password' => [
                'field' => 'invoice_password',
                'label' => lang('invoice_password')
            ]
        ];
    }

    /**
     * Create
     * @param null $db_array
     * @param bool $include_invoice_tax_rates
     * @return null
     */
    public function create($db_array = null, $include_invoice_tax_rates = true)
    {
        $invoice_id = parent::save(null, $db_array);

        $db_array = [
            'invoice_id' => $invoice_id
        ];

        $this->db->insert('ip_invoice_amounts', $db_array);

        if ($include_invoice_tax_rates) {
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

    /**
     * Copies invoice items, tax rates, etc from source to target
     * @param int $sourceId
     * @param int $targetId
     */
    public function copy_invoice($sourceId, $targetId)
    {
        $this->load->model('invoices/Mdl_items');

        $invoice_items = $this->Mdl_items->where('invoice_id', $sourceId)->get()->result();
        foreach ($invoice_items as $invoice_item) {
            $this->Mdl_items->save($targetId, null, [
                'invoice_id'       => $targetId,
                'item_tax_rate_id' => $invoice_item->item_tax_rate_id,
                'item_name'        => $invoice_item->item_name,
                'item_description' => $invoice_item->item_description,
                'item_quantity'    => $invoice_item->item_quantity,
                'item_price'       => $invoice_item->item_price,
                'item_order'       => $invoice_item->item_order
            ]);
        }

        $invoiceTaxRates = $this->Mdl_invoice_tax_rates->where('invoice_id', $sourceId)->get()->result();

        foreach ($invoiceTaxRates as $rate) {
            $this->Mdl_invoice_tax_rates->save($targetId, null, [
                'invoice_id'              => $targetId,
                'tax_rate_id'             => $rate->tax_rate_id,
                'include_item_tax'        => $rate->include_item_tax,
                'invoice_tax_rate_amount' => $rate->invoice_tax_rate_amount
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
        $this->load->model('invoices/Mdl_items');
        $invoice_items = $this->Mdl_items->where('invoice_id', $sourceId)->get()->result();

        foreach ($invoice_items as $invoice_item) {
            $this->Mdl_items->save($targetId, null, [
                'invoice_id'       => $targetId,
                'item_tax_rate_id' => $invoice_item->item_tax_rate_id,
                'item_name'        => $invoice_item->item_name,
                'item_description' => $invoice_item->item_description,
                'item_quantity'    => -$invoice_item->item_quantity,
                'item_price'       => $invoice_item->item_price,
                'item_order'       => $invoice_item->item_order
            ]);
        }

        $invoiceTaxRates = $this->Mdl_invoice_tax_rates->where('invoice_id', $sourceId)->get()->result();

        foreach ($invoiceTaxRates as $rate) {
            $this->Mdl_invoice_tax_rates->save($targetId, null, [
                'invoice_id'              => $targetId,
                'tax_rate_id'             => $rate->tax_rate_id,
                'include_item_tax'        => $rate->include_item_tax,
                'invoice_tax_rate_amount' => -$rate->invoice_tax_rate_amount
            ]);
        }
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        $this->load->model('Mdl_clients');
        if ((!isset($db_array['client_id'])) || (isset($db_array['client_id']) && $db_array['client_id'] == -1)) {
            $db_array_client = [
                'client_name'       => $db_array['client_name'],
                'company_id'        => $this->session->userdata('company_id'),
                'client_reg_number' => $db_array['client_reg_number'],
                'client_address_1'  => $db_array['client_address_1'],
                'client_vat_id'     => $db_array['client_vat_id']
            ];

            $client_id = $this->Mdl_clients->save(null, $db_array_client);

            $db_array['client_id'] = $client_id;
        }

        unset($db_array['client_reg_number']);
        unset($db_array['client_address_1']);
        unset($db_array['client_vat_id']);
        unset($db_array['client_vat_id']);
        unset($db_array['client_name']);

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

    public function delete($invoice_id)
    {
        parent::delete($invoice_id);

        delete_orphans();
    }

    // Used from the guest module, excludes draft and paid

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
        $this->filter_where('ip_invoices_received.invoice_id', $id);
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
                'Mdl_clients',
                'Mdl_companies',
            ]
        );

        $reg_number = $this->Mdl_companies->where('company_id',
            $this->session->userdata('company_id'))->get()->result()[0]->company_code;
        $me_as_client = $this->Mdl_clients->where('client_reg_number', $reg_number)->get()->result();
        $clients_array = [];
        $clients_array[] = -1;

        foreach ($me_as_client as $client) {
            $clients_array[] = $client->client_id;
        }

        $this->filter_where_in('ip_invoices_received.client_id', $clients_array);
        $this->filter_where('ip_invoices_received.is_read_only', 1);
        $this->filter_where('ip_invoices_received.invoice_status_id', 2);

        return $this;
    }

    public function by_client($client_id)
    {
        $this->filter_where('ip_invoices_received.client_id', $client_id);

        return $this;
    }

    public function mark_viewed($invoice_id)
    {

        $this->db->select('invoice_status_id');
        $this->db->where('invoice_id', $invoice_id);

        $invoice = $this->db->get('ip_invoices_received');

        if ($invoice->num_rows()) {
            if ($invoice->row()->invoice_status_id == 2) {
                $this->db->where('invoice_id', $invoice_id);
                $this->db->set('invoice_status_id', 3);
                $this->db->update('ip_invoices_received');
            }

            if ($this->config->item('disable_read_only') == null && $this->Mdl_settings->setting('read_only_toggle') == 'viewed') {
                $this->db->where('invoice_id', $invoice_id);
                $this->db->set('is_read_only', 1);
                $this->db->update('ip_invoices_received');
            }
        }
    }

    public function mark_sent($invoice_id)
    {
        $this->db->select('invoice_status_id');
        $this->db->where('invoice_id', $invoice_id);

        $invoice = $this->db->get('ip_invoices_received');

        if ($invoice->num_rows()) {
            if ($invoice->row()->invoice_status_id == 1) {
                $this->db->where('invoice_id', $invoice_id);
                $this->db->set('invoice_status_id', 2);
                $this->db->update('ip_invoices_received');
            }

            // Set the invoice to read-only if feature is not disabled and setting is sent

            if ($this->config->item('disable_read_only') == null && $this->Mdl_settings->setting('read_only_toggle') == 'sent') {
                $this->db->where('invoice_id', $invoice_id);
                $this->db->set('is_read_only', 1);
                $this->db->update('ip_invoices_received');
            }
        }
    }

    public function invoice_number_check($invoice_number)
    {
        if ($this->id) {
            $old_invoice_number = $this->Mdl_invoices->get_by_id($this->id)->invoice_number;
            if ($old_invoice_number != $invoice_number) {
                $this->db->where('invoice_number', $invoice_number);
                $this->db->where('company_id', $this->session->userdata('company_id'));
                $invoice = $this->db->get('ip_invoices_received');
                if ($invoice->num_rows()) {
                    $this->form_validation->set_message('invoice_number_check', 'Invoice number must be unique');

                    return false;
                } else {

                }
            }

            return true;
        }
    }
}
