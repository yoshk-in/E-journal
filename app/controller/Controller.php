<?php

namespace App\controller;

use App\base\AppHelper;

class Controller
{
    private static $inst;
    private $helper;

    private function __construct()
    {
        $this->helper = AppHelper::class;
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
        $request = $this->helper::getRequest();
        $consoleParser = $this->helper::getConsoleSyntaxParser();
        if ($consoleParser) {
            $consoleParser->parse($request);
        }
        $cmds = \App\command\CommandResolver::getCommand($request);
        foreach ($cmds as $cmd) {
            $cmd->execute($request);
        }
        echo $request->getFeedbackString();
    }

    public static function run()
    {
        $inst = self::init();
        $inst->handleRequest();
    }
}
