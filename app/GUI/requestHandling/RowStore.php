<?php


namespace App\GUI\requestHandling;



use App\GUI\components\Cell;

class RowStore
{
    private array $rowStore = [];
    private array $selected = [];



    public function add(int $id, $row)
    {
        $this->rowStore[$id] = $row;
    }

    public function get(int $id)
    {
        return $this->rowStore[$id];
    }

    public function pop(int $id)
    {
        $data = $this->get($id);
        unset($this->rowStore[$id]);
        return $data;
    }

    public function addSelectedCell(Cell $cell)
    {
        $this->selected[spl_object_id($cell)] = $cell;
    }

    public function removeSelectedCell(Cell $cell)
    {
        unset($this->selected[spl_object_id($cell)]);
    }

    public function removeSelectedCells()
    {
        $this->selected = [];
    }



    public function getSelectedCells(): array
    {
        return $this->selected;
    }

}