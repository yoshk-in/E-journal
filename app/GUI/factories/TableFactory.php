<?php


namespace App\GUI\factories;


use App\GUI\MouseHandlerMng;
use App\GUI\Table;

class TableFactory
{
    public static function create(MouseHandlerMng $mng, int $offset, int $top, int $height, int $cellWidth, int $wideCell) : Table
    {
        return new Table(
             $mng, $top, $offset, $height, $cellWidth, $wideCell
        );
    }

}