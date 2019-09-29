<?php


namespace App\console\parser;


class BlocksAreArrivedParser extends CommandParserByNumbersParser
{
    private $nextArg = NextArgIndex::PARTIAL;

    public function parse()
    {
        parent::parse();
        $this->partial = $this->request->getCommands()[$this->nextArg];
    }
}