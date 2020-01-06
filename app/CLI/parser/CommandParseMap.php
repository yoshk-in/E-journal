<?php


namespace App\CLI\parser;



interface CommandParseMap
{

    const MOVE_PRODUCT = [MoveParser::class, ProductNumberParser::class];

    const PARTIAL_MOVE_BLOCK = [MoveAtPartial::class, ProductNumberParser::class];

    const RANGE_INFO = [ProductNumberParser::class, RangeInfo::class];

    const BY_PRODUCT_NUMB_CMD = self::RANGE_INFO;

    const DEFAULT = [Info::class];

    const CLEAR_JOURNAL = [ClearJournal::class];

    const PARTY = [PartNumberCMD::class, PartNumberValidator::class];




}