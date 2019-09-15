<?php

namespace App\base;

use App\command\CommandResolver;
use App\console\ConsoleParser;
use App\domain\ProductRepository;
use App\cache\Cache;
use \App\console\Render;
use App\base\ConsoleRequest;
use App\domain\ProcedureMapManager;

class AppHelper
{
    private $multiTone = [];
    private $base = 'App\base\\';
    private $console = 'App\console\\';
    private $domain = 'App\domain\\';
    private static $inst;

    /**
     * AppHelper constructor.
     * @param array $multiTone
     */
    final public function __construct()
    {
    }


    public static function init()
    {
        return self::$inst ?? self::$inst = new AppHelper();
    }

    public function getConsoleRequest(): ConsoleRequest
    {
        return $this->getSingleTone(ConsoleRequest::class);
    }

    public function getConsoleSyntaxParser(): ConsoleParser
    {
        return $this->getSingleTone($this->console . 'ConsoleParser');
    }

    public function getCacheObject(): Cache
    {
        return Cache::init();
    }

    public function getProductRepository($productName, $productMap, $devMode): ProductRepository
    {
        return $this->getSingleTone($this->domain . 'ProductRepository', $productName, $productMap, $devMode);
    }

    public function getCommandResolver(): CommandResolver
    {
        return $this->getSingleTone(CommandResolver::class);
    }

    public function getProcedureMap(): ProcedureMapManager
    {
        return $this->getSingleTone($this->domain . 'ProcedureMapManager');
    }


    public function getRender(): Render
    {
        return $this->getSingleTone($this->console . 'Render');
    }

    private function getSingleTone(string $object, $option1 = null, $option2 = null, $option3 = null)
    {
        if (isset($this->multiTone[$object])) {
            return $this->multiTone[$object];
        }
        return $this->multiTone[$object] = new $object($option1, $option2, $option3);
    }

}
