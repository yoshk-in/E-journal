<?php


namespace App\GUI\grid\traits;


use App\GUI\factories\WrappingVisualObjectFactory;

trait TGridCellConstructor
{
    public function __construct(string $class, array $sizes, array $additions = [], array $onActions = [], string $wrapper = null, $factory = WrappingVisualObjectFactory::class)
    {
        $this->factory = $factory;
        $this->cellClass = $class;
        parent::__construct($sizes);
        $this->onCellActions = $onActions;
        $this->additions = $additions;
        $this->wrapper = $wrapper;
    }

}