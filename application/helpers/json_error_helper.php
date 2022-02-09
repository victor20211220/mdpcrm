<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Get json errors
 * @return array
 */
function json_errors()
{
    $return = [];

    foreach (array_keys($_POST) as $key) {
        if (form_error($key)) {
            $return[$key] = form_error($key);
        }
    }

    return $return;
}
