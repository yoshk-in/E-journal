<?php


namespace App\console\parser;


use Psr\Container\ContainerInterface;

class CommandParserResolver implements Arg
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
        if (preg_match(Arg::BLOCK_NUMBERS, $commandArg))
            return Arg::RANGE_INFO;
        if (is_null($commandArg)) return Arg::DEFAULT_CMD;
        throw new \Exception(' не соблюдён формат ввода');
    }

}