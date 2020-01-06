<?php


namespace App\GUI\grid\style;


use App\GUI\tableStructure\TableRow;
use App\GUI\tableStructure\Table;



class RowStyle extends BasicVisualStyle
{
    public Table $table;
    public ?TableRow $parentRow = null;

    public function __construct(?BasicVisualStyle $style = null)
    {
        is_null($style) ?: $this->props = $style->getBasicProps();
        parent::__construct();
    }

    public function __clone()
    {
        $clone = parent::__clone();
        $clone->table = clone $this->table;
        $clone->parentRow = clone $this->parentRow;
        return $clone;
    }
}