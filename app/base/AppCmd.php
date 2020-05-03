<?php


namespace App\base;



interface AppCmd
{
    const SET_PART_NUMBER = 'Party';
    const CLEAR_JOURNAL = 'ClearJournal';

    const START_PROCEDURE = 'StartProductProcedure';
    const END_PROCEDURE = 'EndProductProcedure';
    const CONCRETE_PRODUCT_INFO = 'RangeInfo';
    const FORWARD = 'arrive or dispatch - no matter';

    const CREATE_PRODUCTS = 'create new product numbers';
    const CREATE_PRODUCT_OR_GENERATE = 'create new one product';
    const CURRENT_PROCEDURE_INFO = 'current proc info';
    const PRODUCT_MOVE = 'product procedure change state';
    const CHANGE_PRODUCT_MAIN_NUMBER = 'change product main number';
    const FIND_UNFINISHED = 'find unfinished products';
}