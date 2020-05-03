<?php

namespace bootstrap;


use App\base\AbstractRequest;
use App\base\CLIRequest;
use App\base\GUIRequest;
use App\controller\CLIController;
use App\controller\Controller;
use App\domain\ProductMap;
use App\events\IEventChannel;
use App\GUI\GUIController;
use App\repository\AfterRequestCallBuffer;
use App\repository\ProductRepository;
use DI\Container;
use DI\ContainerBuilder;
use Exception;
use Psr\Container\ContainerInterface;


class App
{
    const ENV = [
        0 => PRODUCTION_options::class,
        1 => DEV_options::class
    ];

    const CLI = 0;
    const GUI = 1;

    const ENV_MODE_PATH_PREFIX = [
        self::CLI => 'cfg/cli/cli_',
        self::GUI => 'cfg/gui/gui_'
    ];

    const DEFINITIONS = [
        'subscribers.php',
        'injections.php',
        'observables.php'
    ];

    protected static bool $boot = false;


    static ContainerInterface $container;


    public static function addDef(): ContainerBuilder
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions('cfg/database.php');
        $builder->addDefinitions('cfg/app.php');
        self::loadDefinitions($builder, 'cfg/');
        return $builder;
    }

    static public function addEnvDef(ContainerBuilder $builder, string $env)
    {
        self::loadDefinitions($builder, $env);
        $container = $builder->build();
        self::ENV($container->get('app.dev_mode'));
        self::$container = $container;
        return $container;
    }

    static public function getContainer(): Container
    {
        return self::$container;
    }

    static public function loadDefinitions(ContainerBuilder $builder, string $pathPrefix)
    {
        foreach (self::DEFINITIONS as $def) {
            $builder->addDefinitions($pathPrefix . $def);
        }
    }


    static public function runCLI()
    {
        self::bootstrap(self::CLI);
        $container = self::$container;
        $container->set(AbstractRequest::class, $container->get(CLIRequest::class));
        $cli = $container->get(CLIController::class);
        $app = $container->get(Controller::class);
        $cli->setNext($app);
        $cli->run();
    }

    static public function bootstrap(int $env)
    {
        if (self::$boot) return;
        self::$boot = true;
        self::addEnvDef(self::addDef(), self::ENV_MODE_PATH_PREFIX[$env]);
    }

    static public function runGUI()
    {
        self::bootstrap(self::GUI);
        $container = self::$container;
        $container->set(AbstractRequest::class, $container->get(GUIRequest::class));
        $gui = $container->get(GUIController::class);
        $gui->run();
    }

    static private function ENV($mode)
    {
        /** @var DEV_options | PRODUCTION_options $mode */
        $mode = self::ENV[(int)$mode];
        $mode::set();
    }






}