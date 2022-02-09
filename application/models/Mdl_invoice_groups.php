<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_invoice_groups extends Response_Model
{
    const TYPE_DEFAULT = 1;
    const TYPE_RECEIVED = 2;

    public $table = 'ip_invoice_groups';
    public $primary_key = 'ip_invoice_groups.invoice_group_id';

    /**
     * Get types
     * @return array
     */
    public function getTypes()
    {
        return [
            self::TYPE_DEFAULT,
            self::TYPE_RECEIVED
        ];
    }

    /**
     * Validation rules
     * @return array
     */
    public function validation_rules()
    {
        return [
            'invoice_group_name'              => [
                'field' => 'invoice_group_name',
                'label' => lang('name'),
                'rules' => 'required'
            ],
            'company_id'                      => [
                'field' => 'company_id',
                'rules' => 'required'
            ],
            'invoice_group_type'              => [
                'field' => 'invoice_group_type',
                'rules' => 'required'
            ],
            'invoice_group_identifier_format' => [
                'field'  => 'invoice_group_identifier_format',
                'label'  => lang('identifier_format'),
                'rules'  => 'required|callback_Mdl_invoice_groups.validate_identifier_format',
                'errors' => [
                    'validate_identifier_format' => '{{{id}}} is required in indentifier formatting'
                ]
            ],
            'invoice_group_next_id'           => [
                'field' => 'invoice_group_next_id',
                'label' => lang('next_id'),
                'rules' => 'required'
            ],
            'invoice_group_left_pad'          => [
                'field' => 'invoice_group_left_pad',
                'label' => lang('left_pad'),
                'rules' => 'required'
            ]
        ];
    }

    /**
     * Validate that format string includes {{{id}}} identifier
     * @param $format
     * @return bool
     */
    public function validate_identifier_format($format)
    {
        return stristr($format, '{{{id}}}') != false ? true : false;
    }

    /**
     * Get list
     * @param $companyId
     * @param $type
     * @return mixed
     */
    public function getList($companyId, $type)
    {
        $options = $type ?
            ['company_id' => $companyId, 'invoice_group_type' => $type] :
            ['company_id' => $companyId];

        return $this->db
            ->get_where($this->table, $options)
            ->result();
    }

    /**
     * Generate invoice number
     * @param $invoiceGroupId
     * @param bool $setNext
     * @return mixed
     */
    public function generateInvoiceNumber($invoiceGroupId, $setNext = true)
    {
        $invoiceGroup = $this->get_by_id($invoiceGroupId);
        $invoiceIdentifier = $this->parseIdentifierFormat(
            $invoiceGroup->invoice_group_identifier_format,
            $invoiceGroup->invoice_group_next_id,
            $invoiceGroup->invoice_group_left_pad
        );

        if ($setNext) {
            $this->setNextInvoiceNumber($invoiceGroupId);
        }

        return $invoiceIdentifier;
    }

    /**
     * Parse identifier format
     * @param $format
     * @param $nextId
     * @param $leftPad
     * @return mixed
     */
    private function parseIdentifierFormat($format, $nextId, $leftPad)
    {
        if (preg_match_all('/{{{([^{|}]*)}}}/', $format, $template_vars)) {
            foreach ($template_vars[1] as $var) {
                switch ($var) {
                    case 'year' :
                        $replace = date('Y');
                        break;
                    case 'month' :
                        $replace = date('m');
                        break;
                    case 'day' :
                        $replace = date('d');
                        break;
                    case 'id' :
                        $replace = str_pad($nextId, $leftPad, '0', STR_PAD_LEFT);
                        break;
                    default :
                        $replace = '';
                }

                $format = str_replace('{{{' . $var . '}}}', $replace, $format);
            }
        }

        return $format;
    }

    /**
     * Set next invoice number
     * @param $invoiceGroupId
     */
    public function setNextInvoiceNumber($invoiceGroupId)
    {
        $this->db->where($this->primary_key, $invoiceGroupId);
        $this->db->set('invoice_group_next_id', 'invoice_group_next_id + 1', false);
        $this->db->update($this->table);
    }
}
