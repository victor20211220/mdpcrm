<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}


function get_total_paused_time($id)
{
    $ci =& get_instance();
    $differenceInSeconds = 0;
    $tasks_time = $ci->db->query('SELECT * FROM ip_tasks_time WHERE task_id = "' . $id . '" ORDER BY id ASC')->result_array();
    foreach ($tasks_time as $t) {

        if ($t['status'] == 4) {

            $tasks_up = $ci->db->query('SELECT * FROM ip_tasks_time WHERE id > "' . $t['id'] . '" ORDER BY id ASC LIMIT 1')->result_array();
            foreach ($tasks_up as $tu) {


                $timeFirst = strtotime($tu['date']);
                $timeSecond = strtotime($t['date']);
                $differenceInSeconds = $timeSecond - $timeFirst;

            }


        }
    }

    return abs($differenceInSeconds);
}


function nicetime($date = '')
{
    if (empty($date)) {
        return "";
    }
    $testDateStr = strtotime($date);
    $finalDate = date("Y-m-d H:i:s", strtotime("-1 hour", $testDateStr));

    $periods = ["seconda", "minute", "hour", "day", "week", "month", "year", "decade"];
    $lengths = ["60", "60", "24", "7", "4.35", "12", "10"];

    $now = time();
    $unix_date = strtotime($finalDate) + 60 * 60;

    // check validity of date
    if (empty($unix_date)) {
        return "Bad date";
    }

    // is it future date or past date
    if ($now > $unix_date) {
        $difference = $now - $unix_date;
        $tense = "";

    } else {
        $difference = $unix_date - $now;
        $tense = "";
    }

    for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
        $difference /= $lengths[$j];
    }

    $difference = round($difference);

    if ($difference != 1) {
        $periods[$j] .= "s";
    }

    return "{$tense} $difference $periods[$j] ";
}

function time_duration($seconds, $use = null, $zeros = false)
{
    // Define time periods
    $periods = [
        'years'   => 31556926,
        'Months'  => 2629743,
        'weeks'   => 604800,
        'days'    => 86400,
        'hours'   => 3600,
        'minutes' => 60,
        'seconds' => 1
    ];

    // Break into periods
    $seconds = (float)$seconds;
    $segments = [];
    foreach ($periods as $period => $value) {
        if ($use && strpos($use, $period[0]) === false) {
            continue;
        }
        $count = floor($seconds / $value);
        if ($count == 0 && !$zeros) {
            continue;
        }
        $segments[strtolower($period)] = $count;
        $seconds = $seconds % $value;
    }

    // Build the string
    $string = [];
    foreach ($segments as $key => $value) {
        $segment_name = substr($key, 0, -1);
        $segment = $value . ' ' . $segment_name;
        if ($value != 1) {
            $segment .= 's';
        }
        $string[] = $segment;
    }

    return implode(', ', $string);
}


function task_total_time($id)
{
    $ci =& get_instance();
    $i = 0;
    $tasks_time = $ci->db->query('SELECT * FROM ip_tasks_time WHERE task_id = "' . $id . '" ORDER BY id DESC LIMIT 1')->result_array();
    foreach ($tasks_time as $c) {
        $total_time = $c['total_time'];
        $task_status = $c['status'];
        $time_worked = $c['time_worked'];
    }

    if (check_if_task_as_time($id) > 1) {


        if ($task_status == 2) {
            $date = new DateTime(get_task_created($id));
            $total_time = $total_time;


            $date->modify('-' . $total_time . ' seconds'); // can be seconds, hours.. etc
            $date->modify('+' . get_total_paused_time($id) . ' seconds');

            return nicetime($date->format('Y-m-d H:i:s'));

//return get_total_paused_time($id);
            // return nicetime(date('Y-m-d H:i:s',strtotime(get_task_created($id))-get_total_paused_time($id)));

        }
        if ($task_status != 2) {
            //$date = new DateTime(get_task_created($id));
//$date->modify('+'.$total_time.' seconds'); // can be seconds, hours.. etc
            return time_duration($time_worked);


        }

    }

    if (check_if_task_as_time($id) == 1) {
        $date = new DateTime(get_task_created($id));
        $date->modify('-' . $total_time . ' seconds'); // can be seconds, hours.. etc

        return nicetime($date->format('Y-m-d H:i:s'));
    }


    //return date('Y-m-d H:i:s',strtotime($total_time3));
}


function check_if_task_as_time($id)
{
    $ci =& get_instance();
    $has_time = 0;
    $tasks_time = $ci->db->query('SELECT * FROM ip_tasks_time WHERE task_id = "' . $id . '"')->result_array();
    foreach ($tasks_time as $c) {
        $has_time++;
    }

    return $has_time;
}

function task_add_time_worked($id, $status)
{
    $ci =& get_instance();
    $total_time_worked = 0;
    $tasks_time = $ci->db->query('SELECT * FROM ip_tasks_time WHERE task_id = "' . $id . '" ORDER BY id DESC LIMIT 1')->result_array();

    foreach ($tasks_time as $t) {
        $timeFirst = strtotime($t['date']);
        $timeSecond = strtotime(date('Y-m-d H:i:s'));
        $differenceInSeconds = $timeSecond - $timeFirst;
        $timetoadd = $t['total_time'];
    }

    $times = $ci->db->query('SELECT * FROM ip_tasks_time WHERE task_id = "' . $id . '"')->result_array();

    foreach ($times as $tt) {
        $total_time_worked = $total_time_worked + $tt['time_worked'];
    }

    $total_time_worked = $total_time_worked + $differenceInSeconds + $timetoadd;

    $data2['time_worked'] = $total_time_worked;
    $data2['task_id'] = $id;
    $data2['status'] = $status;
    $data2['date'] = date('Y-m-d H:i:s');

    $ci->db->insert('ip_tasks_time', $data2);
}

function task_add_time($id)
{
    $ci =& get_instance();
    $i = 0;
    $differenceInSeconds = 0;
    $total_tasks_time = check_if_task_as_time($id);
    if ($total_tasks_time > 0) {
        $tasks_time = $ci->db->query('SELECT * FROM ip_tasks_time WHERE task_id = "' . $id . '" ORDER BY id DESC LIMIT 1')->result_array();
        foreach ($tasks_time as $t) {
            $i++;

            if ($i == 1) {
                $timeFirst = strtotime($t['date']);
                $timeSecond = strtotime(date('Y-m-d H:i:s'));
                $differenceInSeconds = $timeSecond - $timeFirst;
            }

            if ($i > 1) {
                $timeFirst = strtotime($t['date']);
                $timeSecond = strtotime(date('Y-m-d H:i:s'));
                $differenceInSeconds = $timeSecond - $timeFirst;
                // $diff = time()-strtotime($t['date']);
            }
        }
    }

    if ($total_tasks_time == 0) {
        $tasks_time = $ci->db->query('SELECT * FROM ip_tasks_time WHERE task_id = "' . $id . '" ORDER BY id DESC LIMIT 1')->result_array();
        foreach ($tasks_time as $t) {
            $timeFirst = strtotime($t['date']);
            $timeSecond = strtotime(date('Y-m-d H:i:s'));
            $differenceInSeconds = $timeSecond - $timeFirst;
        }
    }

    $data2['status'] = 2;
    $data2['total_time'] = $differenceInSeconds;
    $data2['task_id'] = $id;
    $data2['date'] = date('Y-m-d H:i:s');

    $ci->db->insert('ip_tasks_time', $data2);
}


function get_company_name($id)
{
    $ci =& get_instance();
    $name = '';
    $clients = $ci->db->query('SELECT * FROM ip_clients WHERE client_id = "' . $id . '"')->result_array();
    foreach ($clients as $c) {
        $companies = $ci->db->query('SELECT * FROM ip_companies WHERE company_id = "' . $c['company_id'] . '"')->result_array();
        foreach ($companies as $cc) {
            $name = $cc['company_name'];
        }
    }

    return $name;
}

function get_client_name($id)
{
    $ci =& get_instance();
    $name = '';
    $clients = $ci->db->query('SELECT * FROM ip_clients WHERE client_id = "' . $id . '"')->result_array();
    foreach ($clients as $c) {
        $name = $c['client_name'];
    }

    return $name;
}


function get_task_created($id)
{
    $ci =& get_instance();
    $time = 0;
    $tasks_time = $ci->db->query('SELECT * FROM ip_tasks_time WHERE task_id = "' . $id . '" ORDER BY id ASC LIMIT 1')->result_array();
    foreach ($tasks_time as $c) {
        $time = $c['date'];
    }

    return $time;
}
