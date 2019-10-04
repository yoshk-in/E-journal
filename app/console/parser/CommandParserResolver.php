<?php


namespace App\console\parser;


use Psr\Container\ContainerInterface;

class CommandParserResolver implements ArgMap
{
    private $appContainer;


    public function __construct(ContainerInterface $appContainer)
    {
        $this->appContainer = $appContainer;
    }

    public function getCommandParser(?string $commandArg, array $commandMap): CommandParser
    {
        $commandParserName = $this->findParserName($commandArg, $commandMap);
        $commandParser = $this->appContainer->get($commandParserName);
        return $commandParser;
    }

    private function findParserName(?string $commandArg, $commandMap): string
    {
        if (isset($commandMap[$commandArg])) return $commandMap[$commandArg];
        if (preg_match(ArgMap::BLOCK_NUMBERS, $commandArg))
            return ArgMap::BLOCK_NUMBERS_COMMAND;
        if (is_null($commandArg)) return ArgMap::DEFAULT_CMD;
        throw new \Exception(' не соблюдён формат ввода');
    }

}