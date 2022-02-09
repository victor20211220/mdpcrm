<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Date formats
 * @return array
 */
function date_formats()
{
    return [
        'm/d/Y' => [
            'setting' => 'm/d/Y',
            'datepicker' => 'mm/dd/yyyy'
        ],

        'm-d-Y' => [
            'setting' => 'm-d-Y',
            'datepicker' => 'mm-dd-yyyy'
        ],

        'm.d.Y' => [
            'setting' => 'm.d.Y',
            'datepicker' => 'mm.dd.yyyy'
        ],

        'Y/m/d' => [
            'setting' => 'Y/m/d',
            'datepicker' => 'yyyy/mm/dd'
        ],

        'Y-m-d' => [
            'setting' => 'Y-m-d',
            'datepicker' => 'yyyy-mm-dd'
        ],

        'Y.m.d' => [
            'setting' => 'Y.m.d',
            'datepicker' => 'yyyy.mm.dd'
        ],

        'd/m/Y' => [
            'setting' => 'd/m/Y',
            'datepicker' => 'dd/mm/yyyy'
        ],

        'd-m-Y' => [
            'setting' => 'd-m-Y',
            'datepicker' => 'dd-mm-yyyy'
        ],

        'd-M-Y' => [
            'setting' => 'd-M-Y',
            'datepicker' => 'dd-M-yyyy'
        ],

        'd.m.Y' => [
            'setting' => 'd.m.Y',
            'datepicker' => 'dd.mm.yyyy'
        ],

        'j.n.Y' => [
            'setting' => 'j.n.Y',
            'datepicker' => 'd.m.yyyy'
        ]
    ];
}

/**
 * Date from mysql
 * @param $date
 * @param bool $ignorePostCheck
 * @param bool $withTime
 * @return bool|DateTime|string
 */
function date_from_mysql($date, $ignorePostCheck = false, $withTime = false)
{
    $CI = &get_instance();
    $format = $withTime == true ? "Y-m-d H:i:s" : "Y-m-d";

    if ($date <> '0000-00-00') {
        if (!$_POST || $ignorePostCheck) {
            return (DateTime::createFromFormat($format, $date))
                ->format($CI->Mdl_settings->setting('date_format'));
        }

        return $date;
    }

    return '';
}

/**
 * Date from timestamp
 * @param $timestamp
 * @return string
 */
function date_from_timestamp($timestamp)
{
    $CI = &get_instance();
    $date = (new DateTime())->setTimestamp($timestamp);

    return $date->format($CI->Mdl_settings->setting('date_format'));
}

/**
 * Date to mysql
 * @param $date
 * @return string
 */
function date_to_mysql($date)
{
    $CI = &get_instance();
    $date = DateTime::createFromFormat($CI->Mdl_settings->setting('date_format'), $date);

    return $date->format('Y-m-d');
}

/**
 * Date format settings
 * @return mixed
 */
function date_format_setting()
{
    $CI = &get_instance();
    $date_format = $CI->Mdl_settings->setting('date_format');
    $date_formats = date_formats();

    return $date_formats[$date_format]['setting'];
}

/**
 * Date format for datepicker
 * @return mixed
 */
function date_format_datepicker()
{
    $CI = &get_instance();
    $date_format = $CI->Mdl_settings->setting('date_format');
    $date_formats = date_formats();

    return $date_formats[$date_format]['datepicker'];
}

/**
 * Adds interval to user formatted date and returns user formatted date
 * @param $date
 * @param $increment
 * @return string
 */
function increment_user_date($date, $increment)
{
    $CI = &get_instance();

    return (new DateTime(date_to_mysql($date)))
        ->add(new DateInterval('P' . $increment))
        ->format($CI->Mdl_settings->setting('date_format'));
}

/**
 * Adds interval to yyyy-mm-dd date and returns in same format
 * @param $date
 * @param $increment
 * @return string
 */
function increment_date($date, $increment)
{
    return (new DateTime($date))
        ->add(new DateInterval('P' . $increment))
        ->format('Y-m-d');
}
