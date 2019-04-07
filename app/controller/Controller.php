<?php

namespace App\controller;

class Controller
{
    private static $inst;

    private function __construct()
    {
    }

    public static function init()
    {
        if (self::$inst === null) {
            self::$inst = new Controller();
        }
        return self::$inst;
    }

    public function handleRequest()
    {
        $request = \App\base\AppHelper::getRequest();
        $cmd     = \App\command\CommandResolver::getCommand($request);
        $cmd->execute($request);
        echo $request->getFeedbackString();
    }

    public static function run()
    {
        $inst = self::init();
        $inst->handleRequest();
    }
}
