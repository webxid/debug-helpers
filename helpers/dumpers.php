<?php

namespace WebXID\DebugHelpers;

#region Dump functions

/**
 * Makes dump which is adapt for review on browser web page and in page source;
 * Use this function if you are offline and/or _dump() doesn't work correct
 *
 * @param mixed $data
 * @param string $var_title
 * @param bool $has_die
 * @param bool $var_dump
 * @param bool $echo // if FALSE - returns a dump data
 *
 * @return string|void
 */
function _dump($data, $has_die = true, $var_dump = false, string $var_title = null, $echo = true)
{
    if (!Environment::isAllowedIP()) {
        return;
    }

    if (!$var_dump) {
        $buff = print_r($data, true);
    } else {
        ob_start();
        var_dump($data);
        $buff = ob_get_clean();
    }

    $debug_backtrace = debug_backtrace();
    $file_route = 'Route: ' . $debug_backtrace[0]['file'] . ':' . $debug_backtrace[0]['line'];

    if ($var_title === null) {
        $var_title = 'line #' . $debug_backtrace[0]['line'] . '';
    }

    $dumps_html =
        "<pre style=\"margin: 0;\">\n" .
        "DeBuging Dump\n" .
        "=============\n" .
        "Title: " . $var_title . "\n" .
        "===================================\n\n" .
        $buff . "\n\n" .
        "===================================\n" .
        $file_route . "\n" .
        "Loaded in: " . _trackTime() . "\n" .
        "\n\n</pre>";

    if ($echo) {
        echo $dumps_html;
    } else {
        return $dumps_html;
    }

    if ($has_die) {
        exit();
    }
}

/**
 * Makes dump which is adapt for review in browser consol log  (`consol.log(dump_data);`) and for CRI
 *
 * @param mixed $data
 * @param string $var_title
 * @param bool $has_die
 * @param bool $var_dump
 * @param bool $echo // if FALSE - returns a dump data
 *
 * @return string|void
 */
function __dump($data, $has_die = true, $var_dump = false, string $var_title = null, $echo = true)
{
    if (!$var_dump) {
        $buff = print_r($data, true);
    } else {
        ob_start();
        var_dump($data);
        $buff = ob_get_clean();
    }

    $debug_backtrace = debug_backtrace();
    $file_route = 'Route: ' . $debug_backtrace[0]['file'] . ':' . $debug_backtrace[0]['line'];

    if ($var_title === null) {
        $var_title = 'line #' . $debug_backtrace[0]['line'] . '';
    }

    $dumps_html = "\nDeBuging Dump\n=============\nTitle: " . $var_title . "\n" . "===================================\n\n" . htmlentities($buff) . "\n\n" . "===================================\n" . $file_route . "\n" . "\n\n";

    if ($echo) {
        echo $dumps_html;
    } else {
        return $dumps_html;
    }

    if ($has_die) {
        exit();
    }
}

#endregion

#region Trace

/**
 * Makes dump which is adapt for review on browser web page and in page source;
 * Use this function if you are offline and/or _dump() doesn't work correct
 *
 * @param mixed $data
 * @param bool $has_die
 * @param false $var_dump
 * @param string $dump_function
 */
function _backtrace($data = null, $has_die = true, $var_dump = false, $dump_function = '_dump')
{
    $data = (string) $data ?: json_encode($data);

    $e = new \InvalidArgumentException($data);

    $trace = explode( "\n", $e->getTraceAsString());

    $full_trace = $e->getTrace();

    if (isset($full_trace[1]['function'])) {
        switch ($full_trace[1]['function']) {
            case '__trace':
                unset($trace[0]);
        }
    }

    $dump_function = 'WebXID\DebugHelpers\\' . $dump_function;

    $dump_function([
        'message' => json_decode($data, TRUE),
        'trace' => $trace,
    ], $has_die, $var_dump);
}

/**
 * Makes dump which is adapt for review on browser web page and in page source;
 * Use this function if you are offline and/or _dump() doesn't work correct
 *
 * @param mixed $data
 * @param bool $has_die
 * @param false $var_dump
 */
function __backtrace($data, $has_die = true, $var_dump = false)
{
    _backtrace($data, $has_die, $var_dump, '__dump');
}

#endregion

#region Tracking functions

/**
 * Returns time in seconds with milliseconds
 *
 * @return float
 */
function _mTime()
{
    [$usec, $sec] = explode(' ', microtime());

    return (float) $usec + (float) $sec;
}

/**
 * Counting time of a script execution speed
 *
 * @example
 *    _trackTime('Time of testing');
 *        //some code
 *    echo _trackTime('Time of testing');
 *    echo _trackTime(true);
 *
 * @param string|true $script_key
 * @param int $number_size
 * @param bool $show_unit - TRUE if needs to return unit of measurement (b)
 *
 * @return string
 *
 */
function _trackTime($script_key = 'script_load_time', $number_size = 6, $show_unit = true)
{
    if (!is_string($script_key) && $script_key !== true) {
        throw new \InvalidArgumentException('Invalid $script_key');
    }

    $number_size = (int) $number_size;

    if ($number_size < 1) {
        $number_size = 6;
    }

    static $trackTime;

    if ($script_key === true) {
        $result = '';

        if ($trackTime) {
            foreach ($trackTime as $script_key => $time) {
                $result .= $script_key . ': ' . _trackTime($script_key, $number_size, $show_unit) . "\n";
            }
        }

        return $result;
    }

    if (!isset($trackTime[$script_key]['start'])) {
        $trackTime[$script_key]['start'] = _mTime();
        $trackTime[$script_key]['time'] = 0;
    } else {
        $trackTime[$script_key]['time'] = (_mTime() - $trackTime[$script_key]['start']);

        return number_format($trackTime[$script_key]['time'], $number_size) . ($show_unit ? ' s' : '');
    }

    return number_format($trackTime[$script_key]['time'], $number_size) . ($show_unit ? ' s' : '');
}

/**
 * Tracks used memory
 *
 * ToDo: needs for review. I'm not sure it returns a correct data
 *
 * @example
 * 		_trackMemory('my_script'); //start it track used memory
 * 		//some code
 * 		echo _trackMemory('my_script'); //print value of used memory
 *
 * 		//Print all collected tracks
 * 		echo _trackMemory(true);
 *
 * @param string|true $script_key - title of script part
 * @param bool $show_unit - needs to return unit of measurement (b)
 *
 * @return string
 */
function _trackMemory($script_key = 'script_load_memory', $show_unit = true)
{
    if (!is_string($script_key) && $script_key !== true) {
        throw new \InvalidArgumentException('Invalid $script_key');
    }

    static $trackMemory;

    if ($script_key === true) {
        $result = '';

        if ($trackMemory) {
            foreach ($trackMemory as $script_key) {
                $result .= $script_key . ': ' . _trackMemory($script_key, $show_unit) . "\n";
            }

            $result .= 'Peak: ' . memory_get_peak_usage() . ($show_unit ? ' b' : '');
        }

        return $result;
    }

    if (!isset($trackMemory[$script_key])) {
        $trackMemory[$script_key] = - memory_get_usage();
    } else {
        $trackMemory[$script_key] += memory_get_usage();
    }

    if ($trackMemory[$script_key] < 0) {
        return 0 . ($show_unit ? ' b' : '');
    }

    return $trackMemory[$script_key] . ($show_unit ? ' b' : '');
}

#endregion

_trackTime();
