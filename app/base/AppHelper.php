<?php

namespace App\base;

use \Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;

class AppHelper
{
    private static $request;

    public static function getRequest(): Request
    {
        if (is_null(self::$request)) {
            self::$request = new Request();
        }
        return self::$request;
    }

    public function getConsoleSyntaxParser()
    {
        return \App\console\ParserResolver::getConsoleParser();
    }

    public static function getCacheObject()
    {
        return \App\cache\Cache::init();
    }

    public static function getCommandResolver()
    {
        return \App\command\CommandResolver::class;
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
        $config = \data\DatabaseConf::getConf();
        $doctrine_conf = Setup::createAnnotationMetadataConfiguration(
            array('app/domain'), $devMode
        );
        return EntityManager::create($config, $doctrine_conf);
    }
}
