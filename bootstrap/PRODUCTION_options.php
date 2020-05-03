<?php


namespace bootstrap;


class PRODUCTION_options
{
    public static function set()
    {
        if (extension_loaded('xdebug')) {
            xdebug_disable();
            ini_set('xdebug.remote_autostart', 0);
            ini_set('xdebug.remote_enable', 0);
            ini_set('xdebug.profiler_enable', 0);
            ini_set('xdebug.var_display_max_depth', 0);
        }
    }
}