<?php


namespace App\GUI\grid;


use App\GUI\grid\traits\RerenderInterface;
use App\GUI\grid\traits\TRerender;

class ReactSpace extends GridSpace implements RerenderInterface
{
    use TRerender;

    function rerender(array $offsets)
    {
        $this->nextRerender();
    }
}