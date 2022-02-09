<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Form_Validation_Model extends MY_Model
{
    /**
     * Form_Validation_Model constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->form_validation->CI =& $this;
    }
}
