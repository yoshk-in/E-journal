<?php


namespace App\console\parser;


class RangeInfoParser extends CommandParserByNumbersMapParser
{
    protected $nextArg = NextArgIndexMap::NUMBERS_IN_RANGE_INFO_COMMAND;
}