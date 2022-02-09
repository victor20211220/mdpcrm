<?php

/**
 * Redirect to
 * @param $fallbackUrlString
 * @param bool $redirect
 * @return mixed
 */
function redirect_to($fallbackUrlString, $redirect = true)
{
    $CI = &get_instance();

    $redirectUrl = ($CI->session->userdata('redirect_to')) ?
        $CI->session->userdata('redirect_to') : $fallbackUrlString;

    if ($redirect) {
        redirect($redirectUrl);
    }

    return $redirectUrl;
}

/**
 * Redirect to set
 */
function redirect_to_set()
{
    $CI = &get_instance();
    $CI->session->set_userdata('redirect_to', $CI->uri->uri_string());
}
