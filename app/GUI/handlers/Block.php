<?php


namespace App\GUI\handlers;


use App\GUI\tableStructure\TableRow;

class Block
{
    public static function row(TableRow $row)
    {
        $row->blockRow(true);
        $row->getActiveCell()->blockClick(true);
    }

    public static function unblock(TableRow $row)
    {
        $row->blockRow(false);
        $row->getActiveCell()->blockClick(false);
    }
}