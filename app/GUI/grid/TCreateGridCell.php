<?php


namespace App\GUI\grid;


trait TCreateGridCell
{
    protected $onCellActions;
    protected $additions = [];
    protected $factory;
    protected $cellClass;
    protected $component;

    protected function createCell($offsets)
    {
        $this->component = $this->factory::create($this->cellClass, $offsets, $this->sizes, $this->additions);
        foreach ($this->onCellActions as $event => $action) {
            $this->component->on($event, function () use ($action) { $action($this->component); });
        }
    }

    public function getComponent()
    {
        return $this->component;
    }


}