<?php


namespace App\GUI\grid\traits;


use App\GUI\components\IOffset;
use App\GUI\components\traits\TOwnerable;
use App\GUI\grid\GridCellInterface;

trait TReactToAttach
{
    use TOwnerable, TCellDelegator;

    public function toRight(GridCellInterface $rightCell): GridCellInterface
    {
        $this->toRight = $rightCell;
        $this->neighborCells[IOffset::RIGHT] = $rightCell;
        $this->reactToAttach($rightCell);
        return $this;
    }


    public function toDown(GridCellInterface $bottomCell): GridCellInterface
    {
        $this->toDown = $bottomCell;
        $this->neighborCells[IOffset::DOWN] = $bottomCell;
        $this->reactToAttach($bottomCell);
        return $this;
    }


    protected  function reactToAttach(GridCellInterface $child)
    {
        if ($this->getComponent()) {
            $this->getOwner()->react($child);
        }
    }
}