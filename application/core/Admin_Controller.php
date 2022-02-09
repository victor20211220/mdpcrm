<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admin_Controller extends User_Controller
{
    protected $companyId;
    protected $userId;
    protected $userType;

    /**
     * Admin_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct('user_type', [0, 1, 3]);

        $this->companyId = (int) $this->session->userdata('company_id');
        $this->userId = $this->session->userdata('user_id');
        $this->userType = (int) $this->session->userdata('user_type');
    }
}
