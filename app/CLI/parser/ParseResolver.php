<?php


namespace App\CLI\parser;


use App\base\exceptions\WrongInputException;
use Psr\Container\ContainerInterface;

class ParseResolver implements ParseMap
{
    private $appContainer;
    const WRONG_INPUT = ' не соблюдён формат ввода';



    public function __construct(ContainerInterface $appContainer)
    {
        $this->appContainer = $appContainer;
    }

    public function getParserChain(?string $commandArg, $commandMap): Parser
    {
        switch ($commandArg) {
            case null:
                $chain = ParseMap::DEFAULT;
                break;
            case isset($commandMap[$commandArg]):
                $chain = $commandMap[$commandArg];
                break;
            case (bool) preg_match(ParseMap::BLOCK_NUMBERS, $commandArg):
                $chain = ParseMap::BLOCK_NUMBERS_COMMAND;
                break;
            default:
                $this->exception(self::WRONG_INPUT);
        }
        return $this->resolveChain($chain);
    }

    public function getProductNameParser(): ProductName
    {
       $this->appContainer->get(ProductName::class)->setNextHandler($this->appContainer->get(EndParse::class));
       return $this->appContainer->get(ProductName::class);
    }

    private function resolveChain(array $decorators)
    {
        foreach ($decorators as $key => $decorator)
        {
            $resolving[$key] = $this->appContainer->get($decorator);
            if ($key === 0) continue;
            $resolving[$key - 1]->setNextHandler($resolving[$key]);
        }
        return $resolving[0];
    }

    private function exception(string $msg)
    {
        throw new WrongInputException($msg);
    }

}