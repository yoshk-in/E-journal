<?php


namespace App\CLI\parser;



interface CommandParseMap
{

    const MOVE_PRODUCT = [MoveParser::class, ProductNumberParser::class];

    const PARTIAL_MOVE_BLOCK = [MoveAtPartial::class, ProductNumberParser::class];

    const CONCRETE_PRODUCT_INFO = [ProductNumberParser::class, ConcreteProductInfo::class];

    const DEFAULT = [ProcessingProductInfo::class];

    const CLEAR_JOURNAL = [ClearJournal::class];

    const SET_PART_NUMBER = [SetPartNumber::class, PartNumberValidator::class];




}