<?php


namespace App\GUI\grid\style;


use App\GUI\components\WrapVisualObject;

class GridCell extends Style
{
    public Grid $grid;

    public function __construct(?string $guiComponent = null, ?string $componentWrapper = WrapVisualObject::class)
    {
        parent::__construct($guiComponent, $componentWrapper);
    }


    public function toRight()



}