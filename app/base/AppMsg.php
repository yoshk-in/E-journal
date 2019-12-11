<?php


namespace App\base;


use App\domain\CasualNumberStrategy;

interface AppMsg
{
    const ARRIVE = 'Arrive';
    const DISPATCH = 'Dispatch';
    const PRODUCT_INFO = 'Info';
    const RANGE_INFO = 'RangeInfo';
    const PARTY = 'Party';
    const CLEAR_JOURNAL = 'ClearJournal';
    const CLI = 'CLI';
    const GUI = 'GUI';
    const FORWARD = 'arrive or dispatch - no matter';
    const NOT_FOUND = 'NotFoundNumbers';

    const CREATE_PRODUCTS = 'create new product numbers';
    const CREATE_NEW_ONE_PRODUCT = 'create new one product';
    const GUI_INFO = 'info found';
    const CURRENT_PROCEDURE_INFO = 'current proc info';
    const STAT_INFO = 'started and unfinished products';
    const PRODUCT_MOVE = 'product procedure change state';
    const PRODUCT_STARTED = 'product started';
    const PERSIST_NEW = 'persist new objects';
}