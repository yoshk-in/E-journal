<?php

namespace App\controller;


use App\command\CommandResolver;
use App\console\ConsoleParser;
use App\console\Render;
use App\domain\EventChannel;
use Psr\Container\ContainerInterface;


class Controller
{
    private $consoleParser;
    private $render;
    private $commandResolver;
    private $container;

    public function __construct(
        ContainerInterface $container,
        ConsoleParser $consoleParser,
        CommandResolver $commandResolver,
        Render $render
    ) {
        $this->consoleParser = $consoleParser;
        $this->commandResolver = $commandResolver;
        $this->render = $render;
        $this->container = $container;
    }

    public function handleConsoleRequest()
    {
        $this->consoleParser->parseAndFillRequest();
        $commands = $this->commandResolver->getCommand();
        $domain_class = $this->container->get('app.domain_class');
        $this->container->get(EventChannel::class);
        foreach ($commands as $command) {
            $command = $this->container->get($command);
            $output[] = $command->execute($domain_class);
        }
        $this->render->renderCommand(...$output);
    }

    public function run()
    {
        $this->handleConsoleRequest();
    }
}
