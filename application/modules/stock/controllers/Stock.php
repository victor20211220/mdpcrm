<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends Admin_Controller
{
    /**
     * Stock constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_stock',
            'Mdl_stock_history',
            'Mdl_stock_alert',
            'Mdl_families',
            'Mdl_tax_rates',
            'Mdl_products'
        ]);
    }

    /**
     * Index action
     */
    public function index()
    {
        $this->Mdl_stock->filter_where('ip_stock.company_id', $this->companyId);

        $stock = $this->Mdl_stock->get()->result();

        $this->layout
            ->set('stock', $stock)
            ->buffer('content', 'stock/index')
            ->render();
    }

    /**
     * Alerts
     * @param int $page
     */
    public function alerts($page = 0)
    {
        $this->Mdl_stock_alert->filter_where('ip_stock_alert.company_id', $this->companyId);
        $alerts = $this->Mdl_stock_alert->stock_alerts_new();

        $this->layout
            ->set('alerts', $alerts)
            ->buffer('content', 'stock/alerts')
            ->render();
    }

    /**
     * Form
     * @param null $id
     */
    public function form($id = null)
    {
        $readOnly = 0;
        $historyStock = [];

        if ($this->input->post('btn_cancel', true)) {
            redirect('stock');
        }

        if ($this->input->post('btn_submit', true)) {
            $validUpdates = 0;
            foreach ($this->input->post('product_id', true) as $k => $productId) {
                if (empty($productId)) {
                    continue;
                }

                $this->Mdl_products->set_product_stock_and_alert(
                    $productId,
                    $this->input->post('nr_prod_new_stock', true)[$k] +
                    $this->input->post('nr_prod_old_stock', true)[$k],
                    $this->input->post('nr_prod_new_stock_alert', true)[$k]
                );

                $this->Mdl_stock_alert->check_alert($productId, $this->companyId);
                $validUpdates++;
            }

            $_POST['company_id'] = $this->companyId;
            $_POST['stock_products_updated'] = $validUpdates;
            $_POST['stock_user_updated'] = $this->userId;
            $_POST['stock_update_date'] = date("Y-m-d H:i:s");

            if ($this->Mdl_stock->run_validation()) {

                $id = $this->Mdl_stock->save();

                foreach ($this->input->post('product_id', true) as $k => $productId) {
                    if (empty($productId)) {
                        continue;
                    }

                    $this->Mdl_stock_history->save(null, [
                        'company_id'      => $this->companyId,
                        'product_id'      => $productId,
                        'stock_update_id' => $id,
                        'old_stock'       => $this->input->post('nr_prod_old_stock', true)[$k],
                        'new_stock'       =>
                            $this->input->post('nr_prod_old_stock', true)[$k] +
                            $this->input->post('nr_prod_new_stock', true)[$k],
                        'old_stock_alert' => $this->input->post('nr_prod_old_stock_alert', true)[$k],
                        'new_stock_alert' => $this->input->post('nr_prod_new_stock_alert', true)[$k]
                    ]);

                }

                redirect('stock');
            }
        }

        if ($id and !$this->input->post('btn_submit', true)) {
            if (!$this->Mdl_stock->prep_form($id)) {
                show_404();
            }

            $historyStock = $this->Mdl_stock_history
                ->filter_where('ip_stock_history.company_id', $this->companyId)
                ->filter_where('ip_stock_history.stock_update_id', $id)
                ->get()
                ->result();
            $readOnly = 1;
        }

        if (!$id) {
            $this->Mdl_stock->set_form_value('stock_update_name', 'Stock update in ' . date("Y-m-d H:i:s"));
        }

        $families = $this->Mdl_families
            ->filter_where('ip_families.company_id', $this->companyId)
            ->get()
            ->result();

        $taxRates = $this->Mdl_tax_rates
            ->filter_where('ip_tax_rates.company_id', $this->companyId)
            ->get()
            ->result();

        $products = $this->Mdl_products
            ->filter_where('ip_products.company_id', $this->companyId)
            ->get()
            ->result();

        $check = $this->Mdl_stock->get_by_id($id);
        if ($id && $check->company_id != $this->companyId) {
            show_404();
        }

        $this->layout->set([
            'families'      => $families,
            'tax_rates'     => $taxRates,
            'products'      => $products,
            'history_stock' => $historyStock,
            'read_only'     => $readOnly
        ])
            ->buffer('content', 'stock/form')
            ->render();
    }
}
