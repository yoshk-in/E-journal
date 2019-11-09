<?php


namespace App\GUI\handlers;


use App\GUI\RowCellFactory;

class Block
{
    public static function rowAndActiveCell(RowCellFactory $row)
    {
        $row->blockRow(true);
        $row->getActiveCell()->blockClick(true);
    }

    public static function unblock(RowCellFactory $row)
    {
        $row->blockRow(false);
        $row->getActiveCell()->blockClick(false);
    }
}