<?php


namespace App\console\parser;


class BlocksAreArrivedWithPartialParser extends BlocksAreArrivedParser
{
    protected $nextArg = NextArgIndexMap::PARTIAL;
    const TYPE_CLASS = 'WithPatial';

    public function parse()
    {
        parent::parse();
        $this->partial = $this->request->getConsoleArgs()[$this->nextArg];
    }

    public function getCommand()
    {
        $concrete_command = parent::getCommand();
        return strstr($concrete_command, self::TYPE_CLASS, $before_remove = true);
    }
}