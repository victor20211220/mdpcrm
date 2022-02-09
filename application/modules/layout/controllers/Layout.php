<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Layout extends MX_Controller
{
    public $view_data = [];

    /**
     * Layout constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'Mdl_users_access_resources',
            'Mdl_stock_alert',
            'invoices/Mdl_received_inv_alert',
            'Mdl_tasks_alert'
        ]);
    }

    /**
     * Buffer
     * @return $this
     */
    public function buffer()
    {
        $args = func_get_args();

        if (count($args) == 1) {
            foreach ($args[0] as $arg) {
                $key = $arg[0];
                $view = explode('/', $arg[1]);
                $data = array_merge(isset($arg[2]) ? $arg[2] : [], $this->view_data);

                $this->view_data[$key] = $this->load->view($view[0] . '/' . $view[1], $data, true);
            }
        } else {
            $key = $args[0];
            $view = explode('/', $args[1]);
            $data = array_merge(isset($args[2]) ? $args[2] : [], $this->view_data);

            $this->view_data[$key] = $this->load->view($view[0] . '/' . $view[1], $data, true);
        }

        return $this;
    }

    /**
     * Render
     * @param string $view
     */
    public function render($view = 'layout')
    {
        $userId = $this->session->userdata['user_id'];
        $userResources = $this->Mdl_users_access_resources->get_resources_for_user([$userId]);
        $includedResources = [];

        foreach ($userResources as $resource) {
            $includedResources[] = $resource['resource'];
        }

        $this->set('user_resources', $includedResources);
        $stock = $this->set('stock_alerts', $this->Mdl_stock_alert->get_alerts_number());
        $this->set('invoices_alerts', $this->Mdl_received_inv_alert->get_alerts_number());
        $this->set('assgn_task_alerts', $this->Mdl_tasks_alert->get_alerts_number('1'));
        $this->set('stock_alert', $this->Mdl_tasks_alert->stock_alert('1'));
        $this->set('session_user_type', $this->session->userdata('user_type'));
        $this->set('active_menu', [$this->router->fetch_class() => 1]);

        $this->load->view('layout/' . $view, $this->view_data);
    }

    public function set()
    {
        $args = func_get_args();

        if (count($args) == 1) {
            foreach ($args[0] as $key => $value) {
                $this->view_data[$key] = $value;
            }
        } else {
            $this->view_data[$args[0]] = $args[1];
        }

        return $this;
    }

    /**
     * Simple function to load a view directly using the assigned template
     * Does not use buffering or rendering
     * @param string $view
     * @param array $data
     */
    public function load_view($view, $data = [])
    {
        $view = explode('/', $view);

        $this->load->view($view[0] . '/' . $view[1], $data);
    }
}