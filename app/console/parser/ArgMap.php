<?php


namespace App\console\parser;



interface ArgMap
{

    const BLOCKS_ARE_ARRIVED = BlocksAreArrivedParser::class;

    const BLOCKS_ARE_ARRIVED_WITH_PARTIAL = BlocksAreArrivedWithPartialParser::class;

    const RANGE_INFO = RangeInfoParser::class;

    const DEFAULT_CMD = FullInfoParser::class;

    const BLOCK_NUMBERS_COMMAND = self::RANGE_INFO;

    const BLOCKS_ARE_DISPATCHED = BlocksAreDispatchedParser::class;

    const CLEAR_JOURNAL = ClearJournalMapParser::class;

    const SET_PART_NUMBER = SetPartNumberParser::class;

    const BLOCK_NUMBERS = '#^(\d{3}|\d{6})(-(\d{3}|\d{6}))?(,(\d{3}|\d{6})(-(\d{3}|\d{6}))?)*$#s';



}