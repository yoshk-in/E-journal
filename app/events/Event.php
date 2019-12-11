<?php


namespace App\events;


interface Event
{
    const PRODUCT_CHANGE_STATE = 'product change him state';
    const GUI_PRODUCT_CHANGED = 'another product has been selected';
    const ALERT = 'GUI ALERT';

}