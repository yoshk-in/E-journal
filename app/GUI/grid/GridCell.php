<?php


namespace App\GUI\grid;


use App\GUI\factories\WrappingVisualObjectFactory;

class GridCell extends AbstractGridCell
{
    use TCreateGridCell;

    public function __construct(string $class, array $sizes, array $additions = [], array $onCellActions = [], $factory = WrappingVisualObjectFactory::class)
    {
        $this->factory = $factory;
        $this->cellClass = $class;
        parent::__construct($sizes);
        $this->onCellActions = $onCellActions;
        $this->additions = $additions;
    }





}