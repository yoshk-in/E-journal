<?php

namespace App\base;

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
        return \App\console\ParserResolver::getConsoleSyntaxParser();
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
        if (file_exists('data/DatabaseConf.php')) {
            $conf = \data\DatabaseConf::class;
        } else {
            throw new AppException('configuration class does not exists');
        }
        $conf = \data\DatabaseConf::getConf();
        $doctrineConf = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array('app/domain'), $devMode);
        return \Doctrine\ORM\EntityManager::create($conf, $doctrineConf);
    }
}
