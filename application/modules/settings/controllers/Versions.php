<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Versions extends Admin_Controller
{
    /**
     * Versions constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Mdl_versions');
    }

    /**
     * Index controller
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->Mdl_versions->paginate(site_url('versions/index'), $page);
        $versions = $this->Mdl_versions->result();

        $this->layout
            ->set('versions', $versions)
            ->buffer('content', 'settings/versions')
            ->render();
    }
}
