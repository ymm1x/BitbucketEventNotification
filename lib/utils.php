<?php
/**
 * Dump variable. Support for multiple arguments.
 */
function d()
{
    $trace = debug_backtrace();
    echo '<pre style="text-align:left;background:#fff;color:#333;border:1px solid #ccc;margin:2px;padding:4px;font-family:monospace;font-size:12px">';
    echo 'debug from: <b>' . $trace[0]['file'] . '</b> on line:<b>' . $trace[0]['line'] . ' </b><br />';
    foreach (func_get_args() as $v) {
        var_dump($v);
    }
    echo '</pre>';
}
