<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Format currency
 * @param $amount
 * @param bool $withCurrencySymbol
 * @return string
 */
function format_currency($amount, $withCurrencySymbol = true)
{
    global $CI;

    $symbol = $CI->Mdl_settings->setting('currency_symbol');
    $symbolPlacement = $CI->Mdl_settings->setting('currency_symbol_placement');
    $separator = format_thousands_separator($CI->Mdl_settings->setting('thousands_separator'));
    $decimal = $CI->Mdl_settings->setting('decimal_point');

    $amount = $amount === null ? 0 : $amount;

    if ($withCurrencySymbol == true) {
        if ($symbolPlacement == 'before') {
            return $symbol . '&nbsp;' . number_format($amount, 2, $decimal, $separator);
        } elseif ($symbolPlacement == 'afterspace') {
            return number_format($amount, 2, $decimal, $separator) . '&nbsp;' . $symbol;
        } else {
            return number_format($amount, 2, $decimal, $separator) . $symbol;
        }
    } else {
        return number_format($amount, 2, $decimal, $separator);
    }
}

/**
 * Format total
 * @param $amount
 * @return string
 */
function format_total($amount)
{
    global $CI;

    $separator = format_thousands_separator($CI->Mdl_settings->setting('thousands_separator'));
    $decimal = $CI->Mdl_settings->setting('decimal_point');

    return number_format($amount, ($decimal) ? 2 : 0, $decimal, $separator);
}

/**
 * Format amount
 * @param null $amount
 * @param bool $removeZeroes
 * @param int $decimals
 * @param null|int $forceDecimals
 * @return null|string
 */
function format_amount($amount = null, $removeZeroes = false, $decimals = 2, $forceDecimals = null)
{
    global $CI;

    $thousands = format_thousands_separator($CI->Mdl_settings->setting('thousands_separator'));
    $decimal = $CI->Mdl_settings->setting('decimal_point');

    if ($amount == '0') {
        return $forceDecimals != null && is_int($forceDecimals) ?
            number_format($amount, $forceDecimals, $decimals, $thousands) :
            0;
    }

    if ($amount) {
        $amount = number_format($amount, ($decimal) ? $decimals : 0, $decimal, $thousands);
        if ($removeZeroes == true) {
            $amount = rtrim(rtrim($amount, '0'), $decimal);
            if (
                $forceDecimals != null && is_int($forceDecimals) &&
                $amount / intval($amount) === 1
            ) {
                $amount = number_format($amount, $forceDecimals, $decimal, $thousands);
            }
        }

        return $amount;
    }

    return null;
}

/**
 * Format amount numeric
 * @param $amount
 * @return null|string
 */
function format_amount_int($amount)
{
    return is_null($amount) == false ? number_format($amount, 0) : null;
}

/**
 * TODO: remove this function
 * Standardize amount
 * @param $amount
 * @return mixed
 */
function standardize_amount($amount)
{
    $amount = str_replace('.', ',', $amount);

    if ($total > 5) {
        global $CI;
        $thousands_separator = format_thousands_separator($CI->Mdl_settings->setting('thousands_separator'));
        $decimal_point = $CI->Mdl_settings->setting('decimal_point');

        $amount = str_replace($thousands_separator, '', $amount);
        $amount = str_replace($decimal_point, '.', $amount);
    } else {
        global $CI;
        $thousands_separator = format_thousands_separator($CI->Mdl_settings->setting('thousands_separator'));
        $decimal_point = $CI->Mdl_settings->setting('decimal_point');

        $amount = str_replace($thousands_separator, '', $amount);
        $amount = str_replace($decimal_point, '.', $amount);
    }

    return $amount;
}

/**
 * Format thousands separator
 * @param $thousandsSeparator
 * @return string
 */
function format_thousands_separator($thousandsSeparator)
{
    return preg_match("/\\s+/", $thousandsSeparator) ? "&nbsp;" : $thousandsSeparator;
}
