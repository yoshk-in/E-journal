<?php


namespace App\GUI\grid\traits;


use function App\GUI\left;
use function App\GUI\top;

trait TRerender
{

    protected function _rerender($newOffsets)
    {
        $this->getComponent()->setLeft(left($newOffsets)) && $this->getComponent()->setTop(top($newOffsets));
    }

    protected function nextRerender()
    {
        foreach ($this->neighborCells as $direction => $cell)
        {
            $this->getOwner()->updateOffsets($cell, $direction);
        }
    }
}