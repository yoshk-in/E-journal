<?php


namespace bootstrap;


class DEV_options
{
    public static function set()
    {
        function assertFailed()
        {
            throw new \Exception('assertion has failed');
        }

        ini_set('xdebug.show_local_vars', 0);
        ini_set('xdebug.collect_vars', 0);
        ini_set('xdebug.collect_params', 0);
        ini_set('xdebug.collect_return', 0);
        ini_set('log_errors_max_len', 10000);
        ini_set('xdebug.auto_trace', 0);
        ini_set('xdebug.max_nesting_level', '150');
        ini_set('xdebug.var_display_max_depth', '4');
        ini_set('xdebug.var_display_max_children', '256');
        ini_set('xdebug.var_display_max_data', '1024');
        ini_set('error_reporting', E_ALL);
        ini_set('assert.active', 1);
        ini_set('assert.bail', 1);
        ini_set('assert.callback', 'assertFailed');
//        set_error_handler(function($errno, $errstr) {
//            // error was suppressed with the @-operator
//            if (0 === error_reporting()) {
//                return false;
//            }
//
//            throw new \Exception($errstr, $errno);
//        });
    }
}