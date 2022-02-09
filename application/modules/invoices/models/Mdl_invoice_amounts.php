<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_invoice_amounts extends CI_Model
{
    /**
     * Mdl_invoice_amounts constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('invoices/Mdl_invoice_tax_rates');
    }

    /**
     * Calculate
     * @param $invoiceId
     */
    public function calculate($invoiceId)
    {
        $invoiceId = intval($invoiceId);
        $query = $this->db->query("
            SELECT
                SUM(item_subtotal)                       AS invoice_item_subtotal,
                SUM(item_tax_total)                      AS invoice_item_tax_total,
                SUM(item_subtotal) + SUM(item_tax_total) AS invoice_total,
                SUM(item_discount)                       AS invoice_item_discount
            FROM ip_invoice_item_amounts
            WHERE item_id IN (
                SELECT item_id
                FROM ip_invoice_items
                WHERE invoice_id = {$invoiceId}
                )
        ");

        $invoiceAmounts = $query->row();

        $invoiceItemSubtotal = $invoiceAmounts->invoice_item_subtotal - $invoiceAmounts->invoice_item_discount;
        $invoiceTotal = $invoiceItemSubtotal + $invoiceAmounts->invoice_item_tax_total;

        $query = $this->db->query("
            SELECT
                SUM(payment_amount) AS invoice_paid
            FROM ip_payments
            WHERE invoice_id = {$invoiceId}
        ");

        $invoicePaid = $query->row()->invoice_paid;

        $query = $this->db->query("
            SELECT
                invoice_discount_amount,
                invoice_discount_percent
            FROM ip_invoices
            WHERE
                invoice_id = {$invoiceId}
        ");

        $invoiceDiscounts = $query->row();
        if ($invoiceDiscounts->invoice_discount_amount > 0) {
            $discountAmount = $invoiceDiscounts->invoice_discount_amount;
        } elseif ($invoiceDiscounts->invoice_discount_percent > 0) {
            $discountAmount = $invoiceTotal * $invoiceDiscounts->invoice_discount_percent / 100;
            $discountAmount = number_format($discountAmount, 2, '.', '');
        } else {
            $discountAmount = 0;
        }

        $invoiceTotal = $invoiceTotal - $discountAmount;

        $data = [
            'invoice_id'             => $invoiceId,
            'invoice_item_subtotal'  => $invoiceItemSubtotal,
            'invoice_item_tax_total' => $invoiceAmounts->invoice_item_tax_total,
            'invoice_total'          => $invoiceTotal,
            'invoice_paid'           => ($invoicePaid) ? $invoicePaid : 0,
            'invoice_balance'        => $invoiceTotal - $invoicePaid
        ];

        $this->db->where('invoice_id', $invoiceId);
        if ($this->db->get('ip_invoice_amounts')->num_rows()) {
            $this->db->where('invoice_id', $invoiceId);
            $this->db->update('ip_invoice_amounts', $data);
        } else {
            $this->db->insert('ip_invoice_amounts', $data);
        }

        $this->calculateInvoiceTaxes($invoiceId);

        if ($data['invoice_balance'] <= 0) {
            $this->db->where('invoice_id', $invoiceId);
            $payment = $this->db->get('ip_payments');
            $paymentMethodId = (isset($payment->payment_method_id) ? $payment->payment_method_id : 0);

            $this->db->where('invoice_id', $invoiceId);
            $this->db->set('invoice_status_id', 4);
            $this->db->set('payment_method', $paymentMethodId);
            $this->db->update('ip_invoices');
        }

        if ($this->config->item('disable_read_only') == null && $data['invoice_balance'] == 0 && $data['invoice_total'] != 0) {
            $this->db->where('invoice_id', $invoiceId);
            $this->db->set('is_read_only', 1);
            $this->db->update('ip_invoices');
        }
    }

    /**
     * Calculate invoice taxes
     * @param $invoiceId
     */
    public function calculateInvoiceTaxes($invoiceId)
    {
        $invoiceId = $this->db->escape($invoiceId);
        $invoiceTaxRates = $this->Mdl_invoice_tax_rates->where('invoice_id', $invoiceId)->get()->result();

        if ($invoiceTaxRates) {
            $invoiceAmount = $this->db->where('invoice_id', $invoiceId)->get('ip_invoice_amounts')->row();
            foreach ($invoiceTaxRates as $taxRate) {
                if ($taxRate->include_item_tax) {
                    $taxRateAmount =
                        ($invoiceAmount->invoice_item_subtotal + $invoiceAmount->invoice_item_tax_total) *
                        ($taxRate->invoice_tax_rate_percent / 100);
                } else {
                    $taxRateAmount = $invoiceAmount->invoice_item_subtotal * $taxRate->invoice_tax_rate_percent / 100;
                }

                $this->db->where('invoice_tax_rate_id', $taxRate->invoice_tax_rate_id);
                $this->db->update('ip_invoice_tax_rates', ['invoice_tax_rate_amount' => $taxRateAmount]);
            }

            $this->db->query("
                UPDATE ip_invoice_amounts
                SET
                    invoice_tax_total = (
                        SELECT SUM(invoice_tax_rate_amount)
                        FROM ip_invoice_tax_rates
                        WHERE invoice_id = {$invoiceId}
                    )
                WHERE invoice_id = {$invoiceId}
            ");

            $invoiceAmount = $this->db->where('invoice_id', $invoiceId)->get('ip_invoice_amounts')->row();
            $invoiceTotal = $invoiceAmount->invoice_total;
            $invoiceBalance = $invoiceAmount->invoice_total - $invoiceAmount->invoice_paid;

            if ($invoiceBalance <= 0) {
                $this->db->where('invoice_id', $invoiceId);
                $this->db->set('invoice_status_id', 4);
                $this->db->update('ip_invoices');
            }

            if ($this->config->item('disable_read_only') == null && $invoiceBalance == 0 && $invoiceTotal != 0) {
                $this->db->where('invoice_id', $invoiceId);
                $this->db->set('is_read_only', 1);
                $this->db->update('ip_invoices');
            }
        } else {
            $this->db->where('invoice_id', $invoiceId);
            $this->db->update('ip_invoice_amounts', ['invoice_tax_total' => '0.00']);
        }
    }

    /**
     * Calculate discount
     * @param $invoiceId
     * @param $invoiceTotal
     * @return float
     */
    public function calculate_discount($invoiceId, $invoiceTotal)
    {
        $this->db->where('invoice_id', $invoiceId);
        $invoice_data = $this->db->get('ip_invoices')->row();

        $total = (float)number_format($invoiceTotal, 2, '.', '');
        $discountAmount = (float)number_format($invoice_data->invoice_discount_amount, 2, '.', '');
        $discountPercent = (float)number_format($invoice_data->invoice_discount_percent, 2, '.', '');

        $total = $total - $discountAmount;
        $total = $total - round(($total / 100 * $discountPercent), 2);

        return $total;
    }

    /**
     * Get status totals
     * @param $companyId
     * @param string $period
     * @return array
     */
    public function getStatusTotals($companyId, $period = '')
    {
        switch ($period) {
            default :
            case 'this-month' :
                $joinCondition = "
                    AND MONTH(ip_invoices.invoice_date_created) = MONTH(NOW())
                    AND YEAR(ip_invoices.invoice_date_created) = YEAR(NOW())
                ";
                break;
            case 'last-month' :
                $joinCondition = "
                    AND MONTH(ip_invoices.invoice_date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
                    AND YEAR(ip_invoices.invoice_date_created) = YEAR(NOW())
                ";
                break;
            case 'this-quarter' :
                $joinCondition = "AND QUARTER(ip_invoices.invoice_date_created) = QUARTER(NOW())";
                break;
            case 'last-quarter' :
                $joinCondition = "AND QUARTER(ip_invoices.invoice_date_created) = QUARTER(NOW() - INTERVAL 1 QUARTER)";
                break;
            case 'this-year' :
                $joinCondition = "AND YEAR(ip_invoices.invoice_date_created) = YEAR(NOW())";
                break;
            case 'last-year' :
                $joinCondition = "AND YEAR(ip_invoices.invoice_date_created) = YEAR(NOW() - INTERVAL 1 YEAR)";
                break;
        }

        $results = $this->db->query("
            SELECT
                ip_invoices.invoice_status_id,
                (
                    CASE ip_invoices.invoice_status_id
                        WHEN 4 THEN SUM(ip_invoice_amounts.invoice_paid)
                        ELSE SUM(ip_invoice_amounts.invoice_balance)
                    END
                ) AS sum_total, COUNT(*) AS num_total
            FROM ip_invoice_amounts
            JOIN ip_invoices ON ip_invoices.invoice_id = ip_invoice_amounts.invoice_id
                {$joinCondition}
            WHERE
                ip_invoices.company_id = $companyId AND
                ip_invoices.is_received = 0
            GROUP BY ip_invoices.invoice_status_id
        ")->result_array();

        $return = [];

        foreach ($this->Mdl_invoices->statuses() as $key => $status) {
            $return[$key] = [
                'invoice_status_id' => $key,
                'class'             => $status['class'],
                'label'             => $status['label'],
                'href'              => $status['href'],
                'sum_total'         => 0,
                'num_total'         => 0
            ];
        }

        foreach ($results as $result) {
            $return[$result['invoice_status_id']] = array_merge($return[$result['invoice_status_id']], $result);
        }

        return $return;
    }
}
