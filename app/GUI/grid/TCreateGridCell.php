<?php


namespace App\GUI\grid;



trait TCreateGridCell
{
    protected $onCellActions;
    protected $additions = [];
    protected $factory;
    protected $cellClass;
    protected $component;
    protected $wrapper;

    protected function createCell($offsets): GridCellInterface
    {
        $this->component = $this->factory::create($this->cellClass, $offsets, $this->sizes, $this->additions, $this->wrapper ?? null);
        foreach ($this->onCellActions as $event => $action) {
            $this->component->on($event, function () use ($action) { $action($this->component); });
        }
        return $this;
    }

    public function getComponent()
    {
        return $this->component;
    }


}