<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_products extends Response_Model
{
    public $table = 'ip_products';
    public $primary_key = 'ip_products.product_id';

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    /**
     * Default order by
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_families.family_name, ip_products.product_name');
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_families', 'ip_families.family_id = ip_products.family_id', 'left');
        $this->db->join('ip_tax_rates', 'ip_tax_rates.tax_rate_id = ip_products.tax_rate_id', 'left');
    }

    /**
     * By product
     * @param $match
     */
    public function by_product($match)
    {
        $this->db->where("(
            LOWER(product_sku) LIKE '%{$match}%' OR
            LOWER(product_name) LIKE '%{$match}%' OR
            LOWER(product_description) LIKE '%{$match}%'
        )");
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'product_id' => [
                'field' => 'product_id',
                'label' => 'product_id'
            ],
            'product_sku'         => [
                'field' => 'product_sku',
                'label' => lang('product_sku'),
                'rules' => 'required'
            ],
            'company_id'          => [
                'field' => 'company_id',
                'label' => lang('company_id'),
                'rules' => 'required'
            ],
            'product_name'        => [
                'field' => 'product_name',
                'label' => lang('product_name'),
                'rules' => 'required'
            ],
            'product_description' => [
                'field' => 'product_description',
                'label' => lang('product_description'),
                'rules' => ''
            ],
            'product_search' => [
                'field' => 'product_search',
                'label' => lang('product_search'),
                'rules' => 'required|callback_Mdl_products.check_product_search',
                'errors' => [
                    'check_product_search' => 'Name is not unique'
                ]
            ],
            'stock'               => [
                'field' => 'stock',
                'label' => lang('stock'),
                'rules' => 'numeric'
            ],
            'stock_alert'         => [
                'field' => 'stock_alert',
                'label' => lang('stock_alert'),
                'rules' => 'numeric'
            ],
            'product_price'       => [
                'field' => 'product_price',
                'label' => lang('product_price'),
                'rules' => 'required'
            ],
            'purchase_price'      => [
                'field' => 'purchase_price',
                'label' => lang('purchase_price'),
                'rules' => ''
            ],
            'family_id'           => [
                'field' => 'family_id',
                'label' => lang('family'),
                'rules' => 'numeric'
            ],
            'tax_rate_id'         => [
                'field' => 'tax_rate_id',
                'label' => lang('tax_rate'),
                'rules' => 'numeric'
            ],
        ];
    }

    /**
     * Check search string
     * @param $name
     * @return bool
     */
    public function check_product_search($name)
    {
        $productId = $this->findProductIdBySearchString($_POST['company_id'], $name);

        if ($productId && $_POST['product_id'] == null) {
            $this->form_validation->set_message('product_name', 'Duplicate product', 'Duplicate product');

            return false;
        }

        if ($productId && $_POST['product_id'] && $productId != $_POST['product_id']) {
            $this->form_validation->set_message('product_name', 'Duplicate product', 'Duplicate product');

            return false;
        }

        return true;
    }

    /**
     * Find product id by search string
     * @param $companyId
     * @param $searchString
     * @return null
     */
    public function findProductIdBySearchString($companyId, $searchString)
    {
        $this->db->select('product_id');
        $this->db->where('product_search', $searchString);
        $this->db->where('company_id', $companyId, $searchString);

        $result = $this->db->get('ip_products')->row();
        if ($result) {
            return $result->product_id;
        }

        return null;
    }

    /**
     * Decrease product stock
     * @param $productId
     * @param $quantity
     */
    public function decrease_product_stock($productId, $quantity)
    {
        $this->db->select('stock');
        $this->db->where('product_id', $productId);

        $product = $this->db->get('ip_products')->row();

        $this->db->where('product_id', $productId);
        $this->db->set('stock', (($product->stock) - ($quantity)));
        $this->db->update('ip_products');
    }

    /**
     * Set product stock and alert
     * @param $productId
     * @param $quantity
     * @param $alert
     */
    public function set_product_stock_and_alert($productId, $quantity, $alert)
    {
        $this->db->where('product_id', $productId);
        $this->db->set('stock', $quantity);
        $this->db->set('stock_alert', $alert);
        $this->db->update('ip_products');
    }

    /**
     * Get cols name for import
     * @return array
     */
    public function getHeaderNamesForImport()
    {
        return [
            ['col' => 'product_sku', 'name' => lang('product_sku'), 'required' => 1],
            ['col' => 'product_name', 'name' => lang('product_name'), 'required' => 1],
            ['col' => 'family_id', 'name' => lang('family'), 'required' => 0],
            ['col' => 'product_description', 'name' => lang('product_description'), 'required' => 0],
            ['col' => 'product_price', 'name' => lang('product_price'), 'required' => 0],
            ['col' => 'purchase_price', 'name' => lang('purchase_price'), 'required' => 0],
            ['col' => 'tax_rate_id', 'name' => lang('tax_rate'), 'required' => 0],
            ['col' => 'stock', 'name' => lang('stock'), 'required' => 0],
            ['col' => 'stock_alert', 'name' => lang('stock_alert'), 'required' => 0]
        ];
    }
}
