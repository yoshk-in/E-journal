<?php


namespace App\GUI\grid;


use App\GUI\components\traits\TOwnerable;
use App\GUI\grid\traits\DelegateInterface;
use App\GUI\grid\traits\RerenderInterface;
use App\GUI\grid\traits\TCellDelegator;
use App\GUI\grid\traits\TGridCellConstructor;
use App\GUI\grid\traits\TRerender;
use function App\GUI\left;
use function App\GUI\size;
use function App\GUI\top;

class ReactCell extends AbstractGridCell implements DelegateInterface, RerenderInterface
{
    use TGridCellConstructor, TCreateGridCell, TCellDelegator, TOwnerable, TRerender;

    public function getMethod($prop, $name, array $arguments = [])
    {
        return $this->callComponent($name, $arguments);
    }

    public function setMethod($prop, $name, array $arguments)
    {
        $this->callComponent($name, $arguments);
        return $this;
    }

    public function setVisible(bool $visible)
    {
        $this->callComponent(__FUNCTION__, [$visible]);
        $this->setNewSizes($visible ? size($this->getWidth(), $this->getHeight()) : size(0,0));
        $this->getOwner()->react($this);
    }


    public function setNewSizes(array $sizes)
    {
        $this->sizes = $sizes;
    }

    public function rerender(array $newOffsets)
    {
        $this->_rerender($newOffsets);
        $this->nextRender();
    }


    protected function nextRerender()
    {
        foreach ($this->neighborCells as $direction => $cell)
        {
            $this->getOwner()->updateOffsets($cell, $direction);
        }
    }


    protected function update()
    {
        $this->getOwner()->react($this);
    }

    public function getId(): int
    {
        return $this->cellId;
    }
}