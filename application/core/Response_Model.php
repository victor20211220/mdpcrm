<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Response_Model extends Form_Validation_Model
{
    /**
     * Save
     * @param null $id
     * @param null $data
     * @param bool $setFlash
     * @return null
     */
    public function save($id = null, $data = null, $setFlash = true)
    {
        if ($id) {
            parent::save($id, $data);
            if ($setFlash == true) {
                $this->session->set_flashdata('alert_success', lang('record_successfully_updated'));
            }
        } else {
            $id = parent::save(null, $data);
            if ($setFlash == true) {
                $this->session->set_flashdata('alert_success', lang('record_successfully_created'));
            }
        }

        return $id;
    }

    /**
     * Delete
     * @param $id
     * @param bool $setFlash
     */
    public function delete($id, $setFlash = true)
    {
        parent::delete($id);
        if ($setFlash == true) {
            $this->session->set_flashdata('alert_success', lang('record_successfully_deleted'));
        }
    }
}
