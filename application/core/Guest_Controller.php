<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Guest_Controller extends User_Controller
{
    public $user_clients = [];

    /**
     * Guest_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct('user_type', 2);

        $this->load->model('Mdl_user_clients');

        $userClients = $this->Mdl_user_clients->assigned_to($this->session->userdata('user_id'))->get()->result();

        if (!$userClients) {
            die(lang('guest_account_denied'));
        }

        foreach ($userClients as $userClient) {
            $this->user_clients[$userClient->client_id] = $userClient->client_id;
        }
    }
}
