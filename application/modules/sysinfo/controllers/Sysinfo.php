<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sysinfo extends Admin_Controller
{
    /**
     * Sysinfo constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * PHP info
     */
    public function php()
    {
        phpinfo();
    }
}
