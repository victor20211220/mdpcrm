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

        $this->load->model([
            'Mdl_clients',
            'Mdl_user_clients'
        ]);
    }

    /**
     * Save user client
     */
    public function save_user_client()
    {
        $userId = $this->input->post('user_id', true);
        $clientName = $this->input->post('client_name', true);
        $client = $this->Mdl_clients->where('client_name', $clientName)->get();

        if ($client->num_rows() == 1) {
            $clientId = $client->row()->client_id;
            if ($userId) {

                $userClient = $this->Mdl_user_clients
                    ->where('ip_user_clients.user_id', $userId)
                    ->where('ip_user_clients.client_id', $clientId)
                    ->get();

                if (!$userClient->num_rows()) {
                    $this->Mdl_user_clients->save(null, [
                        'user_id'   => $userId,
                        'client_id' => $clientId
                    ]);
                }
            } else {
                $userClients = $this->session->userdata('user_clients') ?: [];
                $userClients[$clientId] = $clientId;
                $this->session->set_userdata('user_clients', $userClients);
            }
        }
    }

    /**
     * Load user client table
     */
    public function load_user_client_table()
    {
        $sessionUserClients = $this->session->userdata('user_clients');
        $userId = $this->input->post('user_id', true);

        if ($sessionUserClients) {
            $data = [
                'id'           => null,
                'user_clients' => $this->Mdl_clients
                    ->where_in('ip_clients.client_id', $sessionUserClients)
                    ->get()
                    ->result()
            ];
        } else {
            $data = [
                'id'           => $userId,
                'user_clients' => $this->Mdl_user_clients
                    ->where('ip_user_clients.user_id', $userId)
                    ->get()
                    ->result()
            ];
        }

        $this->layout->load_view('users/partial_user_client_table', $data);
    }
}
