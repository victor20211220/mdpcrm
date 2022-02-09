<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_custom_fields_data extends MY_Model
{
    public $table = 'ip_custom_fields_data';
    public $primary_key = 'ip_custom_fields_data.id_data';

    /**
     * Save custom
     * @param $entityId
     * @param $data
     * @param $entityType
     */
    public function save_custom($entityId, $data, $entityType)
    {
        $this->load->model('Mdl_custom_fields');
        $clientCustomId = null;

        $data['client_id'] = $entityId;

        foreach ($data as $key => $value) {
            if ($key == 'client_id') {
                continue;
            }

            $customField = $this->Mdl_custom_fields->filter_where([
                'custom_field_column' => $key,
                'company_id'          => $this->session->userdata('company_id'),
                'custom_field_table'  => $entityType
            ])->get()->row();

            $customFieldsData = $this->where([
                'id_entity'       => $entityId,
                'field_type'      => $entityType,
                'id_company'      => $this->session->userdata('company_id'),
                'custom_field_id' => $customField->custom_field_id,
            ])->get();

            if ($customFieldsData->num_rows()) {
                $customFieldsDataId = $customFieldsData->row()->id_data;
            } else {
                $customFieldsDataId = null;
            }

            parent::save($customFieldsDataId, [
                'field_type'      => $entityType,
                'id_entity'       => $entityId,
                'id_company'      => $this->session->userdata('company_id'),
                'custom_field_id' => $customField->custom_field_id,
                'value_data'      => $value
            ]);
        }
    }
}
