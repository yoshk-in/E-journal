<?php


namespace App\CLI\parser;



interface ParseMap
{

    const BLOCKS_ARE_ARRIVED = [Arrive::class, Numbers::class, EndParse::class];

    const BLOCKS_ARE_ARRIVED_AT_PARTIAL = [ArriveAtPartial::class, Numbers::class, EndParse::class];

    const RANGE_INFO = [Numbers::class, RangeInfo::class, EndParse::class];

    const BLOCK_NUMBERS_COMMAND = self::RANGE_INFO;

    const DEFAULT = [Info::class, EndParse::class];

    const BLOCKS_ARE_DISPATCHED = [Dispatch::class, Numbers::class, EndParse::class];

    const CLEAR_JOURNAL = [ClearJournal::class, EndParse::class];

    const PARTY = [PartyCMD::class, Party::class, EndParse::class];

    const BLOCK_NUMBERS = '#^(\d{3}|\d{6})(-(\d{3}|\d{6}))?(,(\d{3}|\d{6})(-(\d{3}|\d{6}))?)*$#s';



}