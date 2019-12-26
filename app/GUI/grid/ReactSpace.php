<?php


namespace App\GUI\grid;


use App\GUI\grid\traits\RerenderInterface;
use App\GUI\grid\traits\TRerender;
use function App\GUI\size;

class ReactSpace extends GridSpace implements RerenderInterface
{
    use TRerender;

    function rerender(array $offsets)
    {
        $this->nextRerender();
    }

    public function setVisible(bool $visible)
    {
        $this->setNewSizes($visible ?
            $this->sizes
            :
            size(0,0));
        $this->getOwner()->react($this);
    }

    public function setNewSizes(array $sizes)
    {
        $this->sizes = $sizes;
    }

}