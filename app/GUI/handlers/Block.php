<?php


namespace App\GUI\handlers;


use App\GUI\tableStructure\CellRow;

class Block
{
    public static function rowAndActiveCell(CellRow $row)
    {
        $row->blockRow(true);
        $row->getActiveCell()->blockClick(true);
    }

    public static function unblock(CellRow $row)
    {
        $row->blockRow(false);
        $row->getActiveCell()->blockClick(false);
    }
}