<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Admin_Controller
{
    /**
     * Ajax constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_products',
            'Mdl_families'
        ]);
    }

    /**
     * Product lookups
     */
    public function modal_product_lookups()
    {
        $filterProduct = $this->input->get('filter_product', true);

        if (!empty($filterProduct)) {
            $products = $this->Mdl_products->by_product($filterProduct);
        }

        $products = $this->Mdl_products->result();
        $families = $this->Mdl_families->get()->result();

        $data = [
            'products'       => $products,
            'families'       => $families,
            'filter_product' => $filterProduct
        ];

        $this->layout->load_view('products/modal_product_lookups', $data);
    }

    /**
     * Process product selection
     */
    public function process_product_selections()
    {
        $products = $this->Mdl_products
            ->where('ip_products.company_id', $this->companyId)
            ->get()
            ->result();

        foreach ($products as $p) {
            $p->product_price = format_amount($p->product_price);
        }

        echo json_encode($products);
    }
}
