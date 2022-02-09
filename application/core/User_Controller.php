<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User_Controller extends Base_Controller
{
    /**
     * User_Controller constructor.
     * @param $requiredKey
     * @param $requiredValue
     */
    public function __construct($requiredKey, $requiredValue)
    {
        parent::__construct();

        if (!is_array($requiredValue)) {
            if ($this->session->userdata($requiredKey) != $requiredValue) {
                redirect('sessions/login');
            }
        } else {
            if (!in_array($this->session->userdata($requiredKey), $requiredValue)) {
                redirect('sessions/login');
            }
        }

        if ($this->session->userdata('user_id') == '' OR $this->session->userdata('user_id') === null) {
            redirect('sessions/login');
        }
    }
}
