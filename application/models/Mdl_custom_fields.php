<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_custom_fields extends MY_Model
{
    public $table = 'ip_custom_fields';
    public $primary_key = 'ip_custom_fields.custom_field_id';

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', FALSE);
    }

    /**
     * @return array
     */
    public function custom_tables()
    {
        return [
            'ip_client_custom'  => 'client',
            'ip_invoice_custom' => 'invoice',
            'ip_payment_custom' => 'payment',
            'ip_quote_custom'   => 'quote',
            'ip_user_custom'    => 'user'
        ];
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'custom_field_table' => [
                'field' => 'custom_field_table',
                'label' => lang('table'),
                'rules' => 'required'
            ],
            'custom_field_label' => [
                'field' => 'custom_field_label',
                'label' => lang('label'),
                'rules' => 'required|max_length[50]'
            ],
            'company_id'         => [
                'field' => 'company_id'
            ]
        ];
    }

    /**
     * DB array
     * @return array
     */
    public function db_array()
    {
        $data = parent::db_array();

        $customTables = $this->custom_tables();

        if (strtolower($data['custom_field_label']) == 'id') {
            $custom_field_label = 'field_id';
        } else {
            $custom_field_label = strtolower(str_replace(' ', '_', $data['custom_field_label']));
        }

        $clean_name = preg_replace('/[^a-z0-9_\s]/', '', strtolower(diacritics_remove_diacritics($custom_field_label)));

        $data['custom_field_column'] = $customTables[$data['custom_field_table']] . '_custom_' . $clean_name;

        return $data;
    }

    /**
     * Save
     * @param null $id
     * @param null $data
     * @return null
     */
    public function save($id = null, $data = null)
    {
        $data = ($data) ? $data : $this->db_array();
        return parent::save($id, $data);
    }

    /**
     * Delete
     * @param $id
     */
    public function delete($id)
    {
        parent::delete($id);

        $array = [
            'ip_custom_fields_data.custom_field_id' => $id,
            'ip_custom_fields_data.id_company'      => $this->session->userdata('company_id')
        ];

        $this->db->where($array);
        $this->db->delete('ip_custom_fields_data');
    }

    /**
     * By table
     * @param $table
     * @param null $idEntity
     * @return mixed
     */
    public function by_table($table, $idEntity = null)
    {
        $companyId = $this->session->userdata('company_id');

        $query = "
            SELECT *
            FROM `ip_custom_fields_data`
                LEFT JOIN ip_custom_fields
                    ON ip_custom_fields_data.custom_field_id=ip_custom_fields.custom_field_id
            WHERE
                ip_custom_fields_data.id_company = $companyId AND
                ip_custom_fields_data.field_type = '$table' AND
                ip_custom_fields_data.id_entity  = '$idEntity'

            UNION

            SELECT
                NULL,NULL,NULL,NULL,NULL,NULL,
                ip_custom_fields.*
            FROM ip_custom_fields
            WHERE
                ip_custom_fields.company_id         = $companyId AND
                ip_custom_fields.custom_field_table = '$table' AND
                ip_custom_fields.custom_field_id NOT IN (
                    SELECT M.custom_field_id
                    FROM ip_custom_fields_data M
                    WHERE
                        M.id_company = $companyId AND
                        M.field_type = '$table' AND
                        M.id_entity  = '$idEntity'
                )
        ";

        return $this->db->query($query);
    }
}
