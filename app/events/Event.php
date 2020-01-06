<?php


namespace App\events;


interface Event
{
    const PRODUCT_CHANGE_STATE = 'product changes the state';
    const GUI_PRODUCT_CHANGED = 'another product has been selected';
    const PRODUCT_STARTED = 'product has been processed';
    const PROCEDURE_CHANGE_STATE = 'procedure changes the state';
    const ALERT = 'GUI ALERT';
    const PERSIST_NEW = 'persist new ones in database';

}