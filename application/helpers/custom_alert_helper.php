<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('success')) {
    function success($message)
    {
        $CI =& get_instance();

        return $CI->session->set_flashdata('alert_success', $message);
    }
}

if (!function_exists('info')) {
    function info($message)
    {
        $CI =& get_instance();

        return $CI->session->set_flashdata('alert_info', $message);
    }
}

if (!function_exists('error')) {
    function error($message)
    {
        $CI =& get_instance();

        return $CI->session->set_flashdata('alert_error', $message);
    }
}
