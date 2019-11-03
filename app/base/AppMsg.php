<?php


namespace App\base;


interface AppMsg
{
    const ARRIVE = 'Arrive';
    const DISPATCH = 'Dispatch';
    const INFO = 'Info';
    const RANGE_INFO = 'RangeInfo';
    const PARTY = 'Party';
    const CLEAR_JOURNAL = 'ClearJournal';
    const CLI = 'CLI';
    const GUI = 'GUI';
    const FORWARD = 'arrive or dispatch - no matter';
    const NOT_FOUND = 'NotFoundNumbers';

}