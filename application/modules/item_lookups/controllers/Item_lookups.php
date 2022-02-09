<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Item_lookups extends Admin_Controller
{
    /**
     * Item_lookups constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Mdl_item_lookups');
    }

    /**
     * Index action
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->Mdl_item_lookups->paginate(site_url('item_lookups/index'), $page);
        $item_lookups = $this->Mdl_item_lookups->result();

        $this->layout->set('item_lookups', $item_lookups);
        $this->layout->buffer('content', 'item_lookups/index');
        $this->layout->render();
    }

    /**
     * Form
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel', true)) {
            redirect('item_lookups');
        }

        if ($this->Mdl_item_lookups->run_validation()) {
            $this->Mdl_item_lookups->save($id);
            redirect('item_lookups');
        }

        if ($id and !$this->input->post('btn_submit', true)) {
            if (!$this->Mdl_item_lookups->prep_form($id)) {
                show_404();
            }
        }

        $this->layout->buffer('content', 'item_lookups/form');
        $this->layout->render();
    }

    /**
     * Delete
     * @param $id
     */
    public function delete($id)
    {
        $this->Mdl_item_lookups->delete($id);
        redirect('item_lookups');
    }
}
