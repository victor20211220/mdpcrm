<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    /**
     * Ajax constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Mdl_item_lookups');
    }

    /**
     * Modal item lookups
     */
    public function modal_item_lookups()
    {
        $this->layout->load_view('item_lookups/modal_item_lookups', [
            'item_lookups' => $this->Mdl_item_lookups->get()->result()
        ]);
    }

    /**
     * Process item selections
     */
    public function process_item_selections()
    {
        $items = $this->Mdl_item_lookups
            ->where_in('item_lookup_id', $this->input->post('item_lookup_ids', true))
            ->get()
            ->result();

        foreach ($items as $item) {
            $item->item_price = format_amount($item->item_price);
        }

        echo json_encode($items);
    }
}
