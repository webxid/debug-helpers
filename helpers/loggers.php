<?php

namespace WebXID\DebugHelpers;

/**
 * @example
 *     _cachedLog('db_name', 'users');
 *
 *     print_r(_cachedLog('db_name')); // prints ['users']
 *     print_r(_cachedLog()); // prints all data - `['db_name' => ['users']]`
 *
 * @param null $var_name
 * @param string $value
 *
 * @return array|mixed
 */
function _cachedLog($var_name = null, $value = __FILE__)
{
    static $vars_log;

    if ($var_name === null) {
        return $vars_log ?? [];
    }

    if ($value !== __FILE__) {
        $vars_log[$var_name][] = $value;
    }

    return $vars_log[$var_name];
}

/**
 * @param $data
 * @param bool $has_die
 * @param bool $clean_log
 * @param null $debug_backtrace
 */
function _log($data, bool $has_die = false, bool $clean_log = false, array $debug_backtrace = null)
{
    if ($data instanceof \Throwable) {
        $data = [
            'message' => $data->getMessage(),
            'trace' => $data->getTraceAsString(),
        ];
    }

    $debug_backtrace = $debug_backtrace ?? debug_backtrace();

    $log_filename = Environment::getRootDir() . '/logs';

    if (!file_exists($log_filename))
    {
        // create directory/folder uploads.
        mkdir($log_filename, 0777, true);
    }

    $log_route = $log_filename . '/webxid.log';

    if ($clean_log && is_file($log_route)) {
        unlink($log_route);
    }

    // if you don't add `FILE_APPEND`, the file will be erased each time you add a log
    error_log(
        "\n" .
        "----------------\n" .
        print_r($data, true) . (!is_array($data) ? "\n" : '')  .
        "----------------\n" .
        "Route: " . $debug_backtrace[0]['file'] . ':' . $debug_backtrace[0]['line'] . "\n" .
        "\n",
        3,
        $log_route);

    $has_die && exit;
}

/**
 * @param $data
 */
function _logWithCleanAndDie($data)
{
    _log($data, true, true, debug_backtrace());
}

/**
 * @param $data
 */
function _logAndDie($data)
{
    _log($data, true, false,  debug_backtrace());
}

/**
 * @param $data
 */
function _logWithClean($data)
{
    _log($data, false, true,  debug_backtrace());
}
