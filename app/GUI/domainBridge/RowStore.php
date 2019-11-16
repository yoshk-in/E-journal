<?php


namespace App\GUI\domainBridge;


use App\domain\Product;
use App\events\IListener;
use App\GUI\CellRow;
use App\GUI\Debug;

class RowStore
{
    private $rowStore = [];

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




}