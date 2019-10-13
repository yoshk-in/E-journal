<?php

namespace bootstrap;


use App\base\AbstractRequest;
use App\base\CLIRequest;
use App\base\GUIRequest;
use App\controller\CLIController;
use App\controller\Controller;
use App\controller\GUIController;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;


class App
{
    const ENV = [0 => 'production', 1 => 'dev'];

    public static function bootstrap(): ContainerInterface
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions('cfg/database.php');
        $builder->addDefinitions('cfg/procedure_map.php');
        $builder->addDefinitions('cfg/app.php');
        $builder->addDefinitions('cfg/object_injections.php');
        $container = $builder->build();
        self::configEnv($container->get('app.dev_mode'));
        return $container;
    }

    static public function runCLI()
    {
        $container = self::bootstrap();
        $container->set(AbstractRequest::class, $container->get(CLIRequest::class));
        $cli = $container->get(CLIController::class);
        $app = $container->get(Controller::class);
        $cli->setNextHandler($app);
        $cli->run();
    }

    static public function runGUI()
    {
        $container = self::bootstrap();
        $container->set(AbstractRequest::class, $container->get(GUIRequest::class));
        $gui = $container->get(GUIController::class);
        $app = $container->get(Controller::class);
        $gui->setNextHandler($app);
        $gui->run();
    }

    static private function configEnv($mode)
    {
        $mode = self::ENV[(int)$mode];
        self::$mode();
    }

    static private function dev()
    {
//        ini_set('xdebug.max_nesting_level', '150');
//        ini_set('xdebug.var_display_max_depth', '10');
//        ini_set('xdebug.var_display_max_children', '256');
//        ini_set('xdebug.var_display_max_data', '1024');
    }

    static private function production()
    {
        if (function_exists('xdebug_disable')) {
            xdebug_disable();
        }
    }

}