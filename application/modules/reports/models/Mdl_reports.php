<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_reports extends CI_Model
{
    public function sales_by_client($from_date = null, $to_date = null)
    {
        $this->db->select('client_name');
        $company_id = $this->session->userdata('company_id');

        if ($from_date and $to_date) {
            $from_date = date_to_mysql($from_date);
            $to_date = date_to_mysql($to_date);
            $this->db->select("
                (
                    SELECT COUNT(*)
                    FROM ip_invoices
                    WHERE
                        ip_invoices.client_id = ip_clients.client_id AND
                        invoice_date_created >= " . $this->db->escape($from_date) . " AND
                        invoice_date_created <= " . $this->db->escape($to_date) . " AND
                        ip_invoices.company_id=$company_id AND
                        ip_invoices.is_received=0
                ) AS invoice_count "
            );

            $this->db->select("
                (
                    SELECT SUM(invoice_item_subtotal)
                    FROM ip_invoice_amounts
                    WHERE
                        ip_invoice_amounts.invoice_id IN (
                            SELECT invoice_id
                            FROM ip_invoices
                            WHERE ip_invoices.client_id = ip_clients.client_id and
                                invoice_date_created >= " . $this->db->escape($from_date) . " AND
                                invoice_date_created <= " . $this->db->escape($to_date) . "AND
                                ip_invoices.company_id=$company_id  AND
                                ip_invoices.is_received=0 )
                ) AS sales"
            );

            $this->db->select("
                (
                    SELECT SUM(invoice_total)
                    FROM ip_invoice_amounts
                    WHERE
                        ip_invoice_amounts.invoice_id IN (
                            SELECT invoice_id
                            FROM ip_invoices
                            WHERE
                                ip_invoices.client_id = ip_clients.client_id and
                                invoice_date_created >= " . $this->db->escape($from_date) . " AND
                                invoice_date_created <= " . $this->db->escape($to_date) . " AND
                                ip_invoices.company_id=$company_id  AND
                                ip_invoices.is_received=0)
                ) AS sales_with_tax"
            );

            $this->db->where('
                client_id IN (
                    SELECT client_id
                    FROM ip_invoices
                    WHERE
                        invoice_date_created >=' . $this->db->escape($from_date) . ' AND
                        invoice_date_created <= ' . $this->db->escape($to_date) . 'AND
                        ip_invoices.company_id=' . $company_id . '  AND
                        ip_invoices.is_received=0
                )'
            );
        } else {
            $this->db->select('(SELECT COUNT(*) FROM ip_invoices WHERE ip_invoices.client_id = ip_clients.client_id AND ip_invoices.company_id=' . $company_id . '  AND ip_invoices.is_received=0) AS invoice_count');
            $this->db->select('(SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts WHERE ip_invoice_amounts.invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.client_id = ip_clients.client_id  AND ip_invoices.company_id=' . $company_id . '  AND ip_invoices.is_received=0)) AS sales');
            $this->db->select('(SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE ip_invoice_amounts.invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.client_id = ip_clients.client_id AND ip_invoices.company_id=' . $company_id . '  AND ip_invoices.is_received=0)) AS sales_with_tax');
            $this->db->where('client_id IN (SELECT client_id FROM ip_invoices WHERE ip_invoices.company_id=' . $company_id . '  AND ip_invoices.is_received=0)');
        }
        $this->db->order_by('client_name');

        return $this->db->get('ip_clients')->result();
    }

    public function expenses_by_supplier($from_date = null, $to_date = null)
    {
        $this->db->select('supplier_name');
        $company_id = $this->session->userdata('company_id');

        if ($from_date and $to_date) {
            $from_date = date_to_mysql($from_date);
            $to_date = date_to_mysql($to_date);
            $this->db->select("(SELECT COUNT(*) FROM ip_invoices WHERE ip_invoices.supplier_id = ip_suppliers.supplier_id and invoice_date_created >= " . $this->db->escape($from_date) . " and invoice_date_created <= " . $this->db->escape($to_date) . " AND ip_invoices.company_id=$company_id AND ip_invoices.is_received=1) AS invoice_count ");
            $this->db->select("(SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts WHERE ip_invoice_amounts.invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.supplier_id = ip_suppliers.supplier_id and invoice_date_created >= " . $this->db->escape($from_date) . " and invoice_date_created <= " . $this->db->escape($to_date) . "AND ip_invoices.company_id=$company_id  AND ip_invoices.is_received=1 )) AS sales");
            $this->db->select("(SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE ip_invoice_amounts.invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.supplier_id = ip_suppliers.supplier_id and invoice_date_created >= " . $this->db->escape($from_date) . " and invoice_date_created <= " . $this->db->escape($to_date) . " AND ip_invoices.company_id=$company_id  AND ip_invoices.is_received=1)) AS sales_with_tax");
            $this->db->where('supplier_id IN (SELECT supplier_id FROM ip_invoices WHERE invoice_date_created >=' . $this->db->escape($from_date) . ' and invoice_date_created <= ' . $this->db->escape($to_date) . 'AND ip_invoices.company_id=' . $company_id . '  AND ip_invoices.is_received=1)');
        } else {
            $this->db->select('(SELECT COUNT(*) FROM ip_invoices WHERE ip_invoices.supplier_id = ip_suppliers.supplier_id AND ip_invoices.company_id=' . $company_id . '  AND ip_invoices.is_received=1) AS invoice_count');
            $this->db->select('(SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts WHERE ip_invoice_amounts.invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.supplier_id = ip_suppliers.supplier_id  AND ip_invoices.company_id=' . $company_id . '  AND ip_invoices.is_received=1)) AS sales');
            $this->db->select('(SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE ip_invoice_amounts.invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.supplier_id = ip_suppliers.supplier_id AND ip_invoices.company_id=' . $company_id . '  AND ip_invoices.is_received=1)) AS sales_with_tax');
            $this->db->where('supplier_id IN (SELECT supplier_id FROM ip_invoices WHERE ip_invoices.company_id=' . $company_id . '  AND ip_invoices.is_received=1)');
        }
        $this->db->order_by('supplier_name');

        return $this->db->get('ip_suppliers')->result();
    }

    public function payment_history($from_date = null, $to_date = null)
    {
        $this->load->model('Mdl_payments');

        if ($from_date and $to_date) {
            $from_date = date_to_mysql($from_date);
            $to_date = date_to_mysql($to_date);
            $this->Mdl_payments->where('payment_date >=', $from_date);
            $this->Mdl_payments->where('payment_date <=', $to_date);
        }

        return $this->Mdl_payments
            ->filter_where('ip_payments.company_id', $this->session->userdata('company_id'))
            ->get()
            ->result();
    }

    public function invoice_aging()
    {
        $company_id = $this->session->userdata('company_id');
        $this->db->select('client_name');
        $this->db->select('
            (
                SELECT SUM(invoice_balance)
                FROM ip_invoice_amounts
                WHERE invoice_id IN (
                    SELECT invoice_id
                    FROM ip_invoices
                    WHERE
                        ip_invoices.company_id=' . $company_id . ' AND
                        ip_invoices.is_received=0 AND
                        ip_invoices.client_id = ip_clients.client_id AND
                        invoice_date_due <= DATE_SUB(NOW(),INTERVAL 1 DAY) AND
                        invoice_date_due >= DATE_SUB(NOW(), INTERVAL 15 DAY))
            ) AS range_1', false
        );

        $this->db->select('
            (
                SELECT SUM(invoice_balance)
                FROM ip_invoice_amounts
                WHERE
                    invoice_id IN (
                        SELECT invoice_id
                        FROM ip_invoices
                        WHERE
                            ip_invoices.company_id=' . $company_id . '  AND
                            ip_invoices.is_received=0 AND
                            ip_invoices.client_id = ip_clients.client_id AND
                            invoice_date_due <= DATE_SUB(NOW(),INTERVAL 16 DAY) AND
                            invoice_date_due >= DATE_SUB(NOW(), INTERVAL 30 DAY))
            ) AS range_2', false
        );

        $this->db->select('
            (
                SELECT SUM(invoice_balance)
                FROM ip_invoice_amounts
                WHERE
                    invoice_id IN (
                        SELECT invoice_id
                        FROM ip_invoices
                        WHERE
                            ip_invoices.company_id=' . $company_id . '  AND
                            ip_invoices.is_received=0 AND
                            ip_invoices.client_id = ip_clients.client_id AND
                            invoice_date_due <= DATE_SUB(NOW(),INTERVAL 31 DAY))
            ) AS range_3', false
        );

        $this->db->select('
            (
                SELECT SUM(invoice_balance)
                FROM ip_invoice_amounts
                WHERE
                    invoice_id IN (
                        SELECT invoice_id
                        FROM ip_invoices
                        WHERE
                            ip_invoices.company_id=' . $company_id . '  AND
                            ip_invoices.is_received=0 AND
                            ip_invoices.client_id = ip_clients.client_id AND
                            invoice_date_due <= DATE_SUB(NOW(), INTERVAL 1 DAY))
            ) AS total_balance', false
        );

        $this->db->having('range_1 >', 0);
        $this->db->or_having('range_2 >', 0);
        $this->db->or_having('range_3 >', 0);
        $this->db->or_having('total_balance >', 0);

        return $this->db->get('ip_clients')->result();
    }

    public function sales_by_year(
        $from_date = null,
        $to_date = null,
        $minQuantity = null,
        $maxQuantity = null,
        $taxChecked = false
    ) {
        $company_id = $this->session->userdata('company_id');

        if ($minQuantity == "") {
            $minQuantity = 0;
        }
        if ($from_date == "") {
            $from_date = date("Y-m-d");
        } else {
            $from_date = date_to_mysql($from_date);
        }
        if ($to_date == "") {
            $to_date = date("Y-m-d");
        } else {
            $to_date = date_to_mysql($to_date);
        }
        $from_date_year = intval(substr($from_date, 0, 4));
        $to_date_year = intval(substr($to_date, 0, 4));
        if ($taxChecked == false) {
            if ($maxQuantity) {
                $this->db->select('client_id');
                $this->db->select('client_vat_id AS VAT_ID');
                $this->db->select('client_name as Name');
                $this->db->select('(SELECT SUM(amounts.invoice_item_subtotal) FROM ip_invoice_amounts amounts WHERE amounts.invoice_id IN (SELECT inv.invoice_id FROM ip_invoices inv WHERE inv.company_id=' . $company_id . '   AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created)) AS total_payment',
                    false);
                for ($index = $from_date_year; $index <= $to_date_year; $index++) {
                    $this->db->select('(SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-01-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-02-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-03-%\'))) AS payment_t1_' . $index . '',
                        false);
                    $this->db->select('(SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-04-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-05-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-06-%\'))) AS payment_t2_' . $index . '',
                        false);
                    $this->db->select('(SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-07-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-08-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-09-%\'))) AS payment_t3_' . $index . '',
                        false);
                    $this->db->select('(SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-10-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-11-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-12-%\'))) AS payment_t4_' . $index . '',
                        false);
                }
                $this->db->where('(SELECT SUM(amounts.invoice_item_subtotal) FROM ip_invoice_amounts amounts WHERE amounts.invoice_id IN (SELECT inv.invoice_id FROM ip_invoices inv WHERE inv.company_id=' . $company_id . ' AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . ' <= inv.invoice_date_created AND ' . $this->db->escape($to_date) . ' >= inv.invoice_date_created AND ' . $minQuantity . ' <= (SELECT SUM(amounts2.invoice_item_subtotal) FROM ip_invoice_amounts amounts2 WHERE amounts2.invoice_id IN (SELECT inv2.invoice_id FROM ip_invoices inv2 WHERE inv2.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . ' <= inv2.invoice_date_created AND ' . $this->db->escape($to_date) . ' >= inv2.invoice_date_created)) AND ' . $maxQuantity . ' >= (SELECT SUM(amounts3.invoice_item_subtotal) FROM ip_invoice_amounts amounts3 WHERE amounts3.invoice_id IN (SELECT inv3.invoice_id FROM ip_invoices inv3 WHERE inv3.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . ' <= inv3.invoice_date_created AND ' . $this->db->escape($to_date) . ' >= inv3.invoice_date_created)))) <>0');
            } else {
                $this->db->select('client_id');
                $this->db->select('client_vat_id AS VAT_ID');
                $this->db->select('client_name as Name');
                $this->db->select('(SELECT SUM(amounts.invoice_item_subtotal) FROM ip_invoice_amounts amounts WHERE amounts.invoice_id IN (SELECT inv.invoice_id FROM ip_invoices inv WHERE inv.company_id=' . $company_id . ' AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created)) AS total_payment',
                    false);

                for ($index = $from_date_year; $index <= $to_date_year; $index++) {
                    $this->db->select('(SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-01-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-02-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-03-%\'))) AS payment_t1_' . $index . '',
                        false);
                    $this->db->select('(SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-04-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-05-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-06-%\'))) AS payment_t2_' . $index . '',
                        false);
                    $this->db->select('(SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-07-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-08-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-09-%\'))) AS payment_t3_' . $index . '',
                        false);
                    $this->db->select('(SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-10-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-11-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-12-%\'))) AS payment_t4_' . $index . '',
                        false);
                }
                $this->db->where('(SELECT SUM(amounts.invoice_item_subtotal) FROM ip_invoice_amounts amounts WHERE amounts.invoice_id IN (SELECT inv.invoice_id FROM ip_invoices inv WHERE inv.company_id=' . $company_id . ' AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . ' <= inv.invoice_date_created AND ' . $this->db->escape($to_date) . ' >= inv.invoice_date_created AND ' . $minQuantity . ' <= (SELECT SUM(amounts2.invoice_item_subtotal) FROM ip_invoice_amounts amounts2 WHERE amounts2.invoice_id IN (SELECT inv2.invoice_id FROM ip_invoices inv2 WHERE inv2.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . ' <= inv2.invoice_date_created AND ' . $this->db->escape($to_date) . ' >= inv2.invoice_date_created)))) <>0');
            }
        } else {
            if ($taxChecked == true) {
                if ($maxQuantity) {
                    $this->db->select('client_id');
                    $this->db->select('client_vat_id AS VAT_ID');
                    $this->db->select('client_name as Name');
                    $this->db->select('(SELECT SUM(amounts.invoice_total) FROM ip_invoice_amounts amounts WHERE amounts.invoice_id IN (SELECT inv.invoice_id FROM ip_invoices inv WHERE inv.company_id=' . $company_id . '   AND inv.is_received=0  AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created)) AS total_payment',
                        false);
                    for ($index = $from_date_year; $index <= $to_date_year; $index++) {
                        $this->db->select('(SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-01-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-02-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-03-%\'))) AS payment_t1_' . $index . '',
                            false);
                        $this->db->select('(SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-04-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-05-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-06-%\'))) AS payment_t2_' . $index . '',
                            false);
                        $this->db->select('(SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-07-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-08-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-09-%\'))) AS payment_t3_' . $index . '',
                            false);
                        $this->db->select('(SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-10-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-11-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-12-%\'))) AS payment_t4_' . $index . '',
                            false);
                    }
                    $this->db->where('(SELECT SUM(amounts.invoice_total) FROM ip_invoice_amounts amounts WHERE amounts.invoice_id IN (SELECT inv.invoice_id FROM ip_invoices inv WHERE inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . ' <= inv.invoice_date_created AND ' . $this->db->escape($to_date) . ' >= inv.invoice_date_created AND ' . $minQuantity . ' <= (SELECT SUM(amounts2.invoice_total) FROM ip_invoice_amounts amounts2 WHERE amounts2.invoice_id IN (SELECT inv2.invoice_id FROM ip_invoices inv2 WHERE inv2.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . ' <= inv2.invoice_date_created AND ' . $this->db->escape($to_date) . ' >= inv2.invoice_date_created)) AND ' . $maxQuantity . ' >= (SELECT SUM(amounts3.invoice_total) FROM ip_invoice_amounts amounts3 WHERE amounts3.invoice_id IN (SELECT inv3.invoice_id FROM ip_invoices inv3 WHERE inv3.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . ' <= inv3.invoice_date_created AND ' . $this->db->escape($to_date) . ' >= inv3.invoice_date_created)))) <>0');
                } else {
                    $this->db->select('client_id');
                    $this->db->select('client_vat_id AS VAT_ID');
                    $this->db->select('client_name as Name');
                    $this->db->select('(SELECT SUM(amounts.invoice_total) FROM ip_invoice_amounts amounts WHERE amounts.invoice_id IN (SELECT inv.invoice_id FROM ip_invoices inv WHERE inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created)) AS total_payment',
                        false);

                    for ($index = $from_date_year; $index <= $to_date_year; $index++) {
                        $this->db->select('(SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-01-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-02-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-03-%\'))) AS payment_t1_' . $index . '',
                            false);
                        $this->db->select('(SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-04-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-05-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-06-%\'))) AS payment_t2_' . $index . '',
                            false);
                        $this->db->select('(SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-07-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-08-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-09-%\'))) AS payment_t3_' . $index . '',
                            false);
                        $this->db->select('(SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE invoice_id IN (select invoice_id FROM ip_invoices inv where inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created AND (inv.invoice_date_created LIKE \'%' . $index . '-10-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-11-%\' OR inv.invoice_date_created LIKE \'%' . $index . '-12-%\'))) AS payment_t4_' . $index . '',
                            false);
                    }
                    $this->db->where('(SELECT SUM(amounts.invoice_total) FROM ip_invoice_amounts amounts WHERE amounts.invoice_id IN (SELECT inv.invoice_id FROM ip_invoices inv WHERE inv.company_id=' . $company_id . '  AND inv.is_received=0 AND inv.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . ' <= inv.invoice_date_created AND ' . $this->db->escape($to_date) . ' >= inv.invoice_date_created AND ' . $minQuantity . ' <= (SELECT SUM(amounts2.invoice_total) FROM ip_invoice_amounts amounts2 WHERE amounts2.invoice_id IN (SELECT inv2.invoice_id FROM ip_invoices inv2 WHERE inv2.client_id=ip_clients.client_id AND ' . $this->db->escape($from_date) . ' <= inv2.invoice_date_created AND ' . $this->db->escape($to_date) . ' >= inv2.invoice_date_created)))) <>0');
                }
            }
        }
        $this->db->order_by('client_name');

        return $this->db->get('ip_clients')->result();
    }
}
