<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_templates extends CI_Model
{
    /**
     * Get invoice templates
     * @param string $type
     * @return array
     */
    public function get_invoice_templates($type = 'pdf')
    {
        if ($type == 'pdf') {
            $templates = directory_map(APPPATH . '/views/invoice_templates/pdf', true);
        } elseif ($type == 'public') {
            $templates = directory_map(APPPATH . '/views/invoice_templates/public', true);
        }

        $templates = $this->remove_extension($templates);

        return $templates;
    }

    /**
     * remove extension
     * @param $files
     * @return mixed
     */
    private function remove_extension($files)
    {
        foreach ($files as $key => $file) {
            $files[$key] = str_replace('.php', '', $file);
        }

        return $files;
    }

    /**
     * Get quote templates
     * @param string $type
     * @return array|mixed
     */
    public function get_quote_templates($type = 'pdf')
    {
        if ($type == 'pdf') {
            $templates = directory_map(APPPATH . '/views/quote_templates/pdf', true);
        } elseif ($type == 'public') {
            $templates = directory_map(APPPATH . '/views/quote_templates/public', true);
        }

        $templates = $this->remove_extension($templates);

        return $templates;
    }
}
