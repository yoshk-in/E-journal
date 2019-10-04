<?php


namespace App\console\parser;


class BlocksAreArrivedParser extends CommandParserByNumbersParser
{
    private $nextArg = NextArgIndexMap::PARTIAL;

    public function parse()
    {
        parent::parse();
        $this->partial = $this->request->getCommands()[$this->nextArg];
    }
}