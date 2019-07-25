<?php

namespace App\base;

use App\base\exceptions\ExceptionGenerator;
use App\domain\Product;
use \Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;
use App\command\CommandResolver;
use App\console\ParserResolver;
use App\cache\Cache;
use data\DatabaseConf;
use App\base\exceptions\AppException;


class AppHelper
{
    private static $request;
    private static $exceptionGenerator;

    public static function getRequest(): Request
    {
        if (is_null(self::$request)) {
            self::$request = new Request();
        }
        return self::$request;
    }

    public static function getConsoleSyntaxParser(?string $product = null)
    {
        if ($product) return ParserResolver::getConsoleParser($product);
        return ParserResolver::getConsoleParser();
    }

    public static function getCacheObject()
    {
        return Cache::init();
    }

    public static function getCommandResolver()
    {
        return CommandResolver::class;
    }

    public static function getEntityManager($devMode = true)
    {
        $config_exists = !(file_exists('data/DatabaseConf.php')
            && (class_exists('\data\DatabaseConf')));
        if ($config_exists) {
            throw new AppException(
                'configuration class does not exists in /data dir ' .
                            '"DatabaseConf::getConf()" method required ' .
                "by Doctrine ORM"
            );
        }
        $config =DatabaseConf::getConf();
        $doctrine_conf = Setup::createAnnotationMetadataConfiguration(
            array('app/domain'), $devMode
        );
        return EntityManager::create($config, $doctrine_conf);
    }

    public static function getExceptionGenerator()
    {
        if (is_null(self::$exceptionGenerator)) {
            self::$exceptionGenerator = new ExceptionGenerator();
        }
        return self::$exceptionGenerator;
    }

}
