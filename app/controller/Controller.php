<?php

namespace App\controller;

use App\base\AppHelper;

class Controller
{
    private static $_inst;
    private $_helper;

    private function __construct()
    {
        $this->_helper = AppHelper::class;
    }

    public static function init()
    {
        if (self::$_inst === null) {
            self::$_inst = new Controller();
        }
        return self::$_inst;
    }

    public function handleRequest()
    {
        $request = $this->_helper::getRequest();
        $console_parser = $this->_helper::getConsoleSyntaxParser();
        if ($console_parser) {
            $console_parser->parse($request);
        }
        $commands = \App\command\CommandResolver::getCommand($request);
        foreach ($commands as $command) {
            $command->execute($request);
        }
        echo $request->getFeedbackString();
    }

    public static function run()
    {
        $instance = self::init();
        $instance->handleRequest();
    }
}
