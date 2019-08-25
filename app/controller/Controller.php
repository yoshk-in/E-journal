<?php

namespace App\controller;

use App\base\AppHelper;
use App\command\CommandResolver;
use App\domain\Product;

class Controller
{
    private static $inst;
    private $helper;

    private function __construct()
    {
        $this->helper = new AppHelper;
    }

    public static function init()
    {
        if (self::$inst === null) {
            self::$inst = new Controller();
        }
        return self::$inst;
    }

    public function handleConsoleRequest()
    {
        $request = $this->helper->getconsoleRequest();
        $domain_class = Product::class;
        $console_parser = $this->helper->getConsoleSyntaxParser();
        $cache = $this->helper->getCacheObject();
        $procedure_map = $this->helper->getProcedureMap();
        $console_parser->parseAndFillRequest(
            $request,
            $procedure_map,
            $cache);
        $product_repository = $this->helper->getProductRepository(
            $domain_class,
            $procedure_map,
            $devMode = true
            );
        $commands = $this->helper->getCommandResolver()->getCommand($request);
        foreach ($commands as $command) {
            $output[] = $command->execute(
                $request,
                $product_repository,
                $domain_class,
                $procedure_map
            );
        }
        $render = $this->helper->getRender();
        $render->renderCommand(...$output);
        echo $request->getFeedbackString();
    }

    public static function run()
    {
        $instance = self::init();
        $instance->handleConsoleRequest();
    }
}
