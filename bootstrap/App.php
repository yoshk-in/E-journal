<?php

namespace bootstrap;


use App\base\AbstractRequest;
use App\base\CLIRequest;
use App\base\GUIRequest;
use App\controller\CLIController;
use App\controller\Controller;
use App\controller\GUIController;
use App\GUI\GUIManager;
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
        $gui = $container->get(GUIManager::class);
        $gui->run();
    }

    static private function configEnv($mode)
    {
        $mode = self::ENV[(int)$mode];
        self::$mode();
    }

    static private function dev()
    {
        function assertFailed() {
            throw new \Exception('assertion has failed');
        }
        ini_set('xdebug.max_nesting_level', '150');
        ini_set('xdebug.var_display_max_depth', '4');
        ini_set('xdebug.var_display_max_children', '256');
        ini_set('xdebug.var_display_max_data', '1024');
        ini_set('error_reporting', E_ALL);
        ini_set('assert.active', 1);
        ini_set('assert.bail', 1);
        ini_set('assert.callback', 'assertFailed');
//        set_error_handler(function($errno, $errstr) {
//            // error was suppressed with the @-operator
//            if (0 === error_reporting()) {
//                return false;
//            }
//
//            throw new \Exception($errstr, $errno);
//        });
    }

    static private function production()
    {

        if (extension_loaded('xdebug')) {
            xdebug_disable();
            ini_set('xdebug.remote_autostart',0);
            ini_set('xdebug.remote_enable', 0);
            ini_set('xdebug.profiler_enable',0);
            ini_set('xdebug.var_display_max_depth', 0);
        }
    }

}