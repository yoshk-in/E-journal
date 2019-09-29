<?php


namespace App\console\parser;



interface Arg
{

    const BLOCKS_ARE_ARRIVED = BlocksAreArrivedParser::class;

    const RANGE_INFO = RangeInfoParser::class;

    const DEFAULT_CMD = FullInfoParser::class;

    const BLOCK_ARE_DISPATCHED = BlocksAreDispatchedParser::class;

    const CLEAR_JOURNAL = ClearJournalParser::class;

    const SET_PART_NUMBER = SetPartNumberParser::class;

    const BLOCK_NUMBERS = '#^(\d{3}|\d{6})(-(\d{3}|\d{6}))?(,(\d{3}|\d{6})(-(\d{3}|\d{6}))?)*$#s';

    const REMOVE_PARSER = 'Parser';

}