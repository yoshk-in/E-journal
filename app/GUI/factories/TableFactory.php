<?php


namespace App\GUI\factories;


use App\GUI\MouseMng;
use App\GUI\Table;

class TableFactory
{
    public static function create(int $offset, MouseMng $mng) : Table
    {
        return new Table(
            20, $offset = 20, 50, 100, $wide_cell = 600, $mng
        );
    }

}