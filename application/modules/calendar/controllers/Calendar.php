<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar extends Admin_Controller
{
    /**
     * Calendar constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model(['Mdl_calendar']);
    }

    /**
     * Load index view
     */
    public function index()
    {
        $this->load->view('calendar/index');
    }

    /**
     * Create
     */
    public function create()
    {
        $success = false;
        if ($this->input->method() == 'post') {
            $_POST['user_id'] = $this->userId;
            if ($_POST['fullday'] == true && $_POST['date_start']) {
                $_POST['date_start'] = (new DateTime($_POST['date_start']))->format('Y-m-d 00:00:00');
                $_POST['date_end'] = $_POST['date_start'];
            }

            if ($this->Mdl_calendar->run_validation() == true) {
                $this->Mdl_calendar->save();
                $success = true;
            }
        }

        $this->load->view('calendar/create_update', [
            'action'  => 'create',
            'success' => $success
        ]);
    }

    public function update($id)
    {
        $this->Mdl_calendar->prep_form($id);

        $success = false;
        if ($this->input->method() == 'post') {
            $_POST['user_id'] = $this->userId;
            if ($_POST['fullday'] == true && $_POST['date_start']) {
                $_POST['date_start'] = (new DateTime($_POST['date_start']))->format('Y-m-d 00:00:00');
                $_POST['date_end'] = $_POST['date_start'];
            }

            if ($this->Mdl_calendar->run_validation() == true) {
                $this->Mdl_calendar->save($id);
                $success = true;
            }
        } else {
            $_POST['date_start'] = (new DateTime())->format('Y-m-d H:i:00');
        }

        $this->load->view('calendar/create_update', [
            'id'      => $id,
            'action'  => 'update',
            'success' => $success
        ]);
    }

    /**
     * Delete
     * @param $id
     */
    public function delete($id)
    {
        $this->Mdl_calendar->delete($id);
    }

    public function layout()
    {
        $this->load->view('calendar/layout');
    }

    /**
     * Events
     */
    public function events()
    {
        $array = [];
        $data = $this->Mdl_calendar
            ->filter_where('user_id', $this->userId)
            ->get()
            ->result();

        if ($data) {
            foreach ($data as $d) {
                $array[] = [
                    'id'       => $d->id,
                    'title'    => $d->title,
                    'allDay'   => $d->fullday == true ? true : false,
                    'editable' => true,
                    'start'    => $d->date_start,
                    'end'      => $d->date_end,
                    'color'    => $d->color ? $d->color : '#808080'
                ];
            }
        }

        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }
}
