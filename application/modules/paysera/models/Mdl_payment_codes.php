<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_payment_codes extends CI_Model
{
    public $table = 'ip_payment_codes';
    public $primary_key = 'ip_payment_codes.invoice_id';
    public $date_modified_field = 'invoice_date_modified';

    /**
     * Add code
     * @param $invoiceId
     * @param $code
     */
    public function add_code($invoiceId, $code)
    {
        $this->db->query("INSERT INTO ip_payment_codes(invoice_id, code) VALUES(?, ?)", [$invoiceId, $code]);
    }

    /**
     * Get code by column
     * @param $column
     * @param $value
     * @return mixed
     */
    public function get_code_by_column($column, $value)
    {
        $sql = 'SELECT * FROM ip_payment_codes WHERE ' . $column . '=? AND used = 0';
        $query = $this->db->query($sql, [$value]);

        return $query->result_array();
    }

    /**
     * Update code by invoice Id
     * @param $invoiceId
     * @param $dateUsed
     */
    public function update_code_by_invoice_id($invoiceId, $dateUsed)
    {
        $this->db->query("
            UPDATE ip_payment_codes SET
                used=1,
                date_used=?
            WHERE invoice_id=?", [$dateUsed, $invoiceId]);
    }
}
