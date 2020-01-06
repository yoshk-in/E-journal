<?php


namespace App\GUI\grid;


use App\GUI\grid\traits\RerenderInterface;

class ReactGrid extends Grid
{
    protected array $cells = [];
    protected string $cellExistErr = ' cell not found at grid';


    public function react(AbstractGridCell $cell)
    {
        $this->setNewCellSizes($cell, $cell->getSizes());
        foreach ($cell->getChildren() as $yieldChild) {
            $direction = array_key_first($yieldChild);
            $child = $yieldChild[$direction];
            $this->updateOffsets($child, $direction);
        }
    }

    public function updateOffsets(RerenderInterface $cell, string $direction)
    {
        $parentId = $this->getParentId($cell);
        $oldOffsets = $this->getCellSizes($cell->getId())[0];
        $newOffsets = $this->computeChildOffsets($parentId, $direction);
        $newOffsets === $oldOffsets ?: $this->setNewCellOffsets($cell, $newOffsets) && $cell->rerender($newOffsets);
    }

    public function getParentId(AbstractGridCell $cell)
    {
        return $cell->getId() - 1;
    }

    public function getParentSizes(AbstractGridCell $cell): array
    {
        return $this->grid[$this->getParentId($cell)];
    }

    protected function setNewCellOffsets(AbstractGridCell $cell, array $offsets)
    {
        assert(isset($this->grid[$cell->getId()]), $this->cellExistErr);
        $sizes = $this->grid[$cell->getId()][1];
        return $this->grid[$cell->getId()] = [$offsets, $sizes];
    }

    protected function setNewCellSizes(AbstractGridCell $cell, array $sizes)
    {
        assert(isset($this->grid[$cell->getId()]), $this->cellExistErr);
        $offsets = $this->grid[$cell->getId()][0];
        return $this->grid[$cell->getId()] = [$offsets, $sizes];
    }

    protected function updateSizes(AbstractGridCell $cell)
    {
        $this->getCellSizes($cell->getId());
    }

    protected function _createCell($createClosure, $offsets)
    {
        $this->cells[] = $cell = $createClosure($offsets);
        $cell->setOwner($this);
    }

    public function getCell(AbstractGridCell $cell)
    {
        assert(isset($this->cells[$cell->getId()]), $this->cellExistErr);
        return $this->cells[$cell->getId()] ;
    }



}