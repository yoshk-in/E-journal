<?php


namespace App\events;

/**
 * Interface IEvent
 * @package App\events
 * @property string $class
 * @property IObservable $observable
 */
interface IEvent
{
    const GUI_PRODUCT_CHANGED = 'another product has been selected';
    const PRODUCT_STARTED = 'product has been processed';
    const PROCEDURE_CHANGE_STATE = 'procedure changes the state';
    const ALERT = 'GUI ALERT';
    const PRODUCT_CHANGE_NUMBER = 'product has changed number';
    const PRODUCT_INFO = 'product info';
    const CONCRETE_PRODUCT_INFO = 'concrete product info';
    const PROCESSING_PRODUCT_AND_NOT_STARTED_INFO = 'info found';
    const CURRENT_PROCEDURE_INFO = 'current proc info';



}