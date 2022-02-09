<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_uploads extends Response_Model
{
    public $table = 'ip_uploads';
    public $primary_key = 'ip_uploads.upload_id';
    public $date_modified_field = 'uploaded_date';

    /**
     * Mdl_uploads constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'quotes/Mdl_quotes',
            'invoices/Mdl_invoices'
        ]);
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_uploads.upload_id ASC');
    }

    /**
     * Default join
     */
    public function default_join()
    {
    }

    /**
     * TODO: remove this
     * @param null $data
     * @return null
     */
    public function create($data = null)
    {
        return parent::save(null, $data);
    }

    /**
     * Get quote uploads
     * @param $id
     * @return array
     */
    public function get_quote_uploads($id)
    {
        $quote = $this->Mdl_quotes->get_by_id($id);
        $query = $this->db->query("
            SELECT
                file_name_new,
                file_name_original
            FROM ip_uploads
            WHERE url_key = '{$quote->quote_url_key}'
        ");

        $names = [];
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                array_push($names, [
                    'path'     => getcwd() . '/uploads/customer_files/' . $row->file_name_new,
                    'filename' => $row->file_name_original
                ]);
            }
        }

        return $names;
    }

    /**
     * Get invoice uploads
     * @param $id
     * @return array
     */
    public function get_invoice_uploads($id)
    {
        $invoice = $this->Mdl_invoices->get_by_id($id);
        $query = $this->db->query("
            SELECT
                file_name_new,
                file_name_original
            FROM {$this->table}
            WHERE url_key = '{$invoice->invoice_url_key}'
        ");

        $names = [];
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                array_push($names, [
                    'path'     => getcwd() . '/uploads/customer_files/' . $row->file_name_new,
                    'filename' => $row->file_name_original
                ]);
            }
        }

        return $names;
    }

    /**
     * Delete
     * @param $urlKey
     * @param bool $filename
     */
    public function delete($urlKey, $filename)
    {
        $this->db->where('url_key', $urlKey);
        $this->db->where('file_name_original', $filename);
        $this->db->delete('ip_uploads');
    }

    /**
     * By client
     * @param $clientId
     * @return $this
     */
    public function by_client($clientId)
    {
        $this->filter_where('ip_uploads.client_id', $clientId);

        return $this;
    }
}
