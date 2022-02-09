<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    /**
     * Ajax constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('Rekvizitai');
        $this->load->model([
            'clients/Mdl_clients',
            'Mdl_client_notes'
        ]);
    }

    /**
     * Name query
     */
    public function name_query()
    {
        $query = $this->input->post('query', true);
        $response = [];

        $this->Mdl_clients->filter_where('ip_clients.company_id', $this->companyId);
        $clients = $this->Mdl_clients
            ->select('client_name')
            ->like('client_name', $query)
            ->order_by('client_name')
            ->get([], false)
            ->result();

        foreach ($clients as $client) {
            $response[] = $client->client_name;
        }

        echo json_encode($response);
    }

    /**
     * Save client note
     */
    public function save_client_note()
    {
        if ($this->Mdl_client_notes->run_validation()) {
            $this->Mdl_client_notes->save();
            $response = ['success' => 1];
        } else {
            $response = [
                'success' => 0,
                'validation_errors' => json_errors()
            ];
        }

        echo json_encode($response);
    }

    /**
     * Load client notes
     */
    public function load_client_notes()
    {
        $notes = $this->Mdl_client_notes
            ->where('client_id', $this->input->post('client_id', true))
            ->get()
            ->result();

        $this->layout->load_view('clients/partial_notes', ['client_notes' => $notes]);
    }

    /**
     * Api search
     * @throws Exception
     */
    public function api_search()
    {
        $name = $this->input->post('name');
        $number = $this->input->post('number');

        $rekvizitai = new Rekvizitai();
        $searchResult = $rekvizitai->search($this->session->userdata('user_id'), $name, $number);

        echo json_encode($searchResult);
    }
}
