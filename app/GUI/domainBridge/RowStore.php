<?php


namespace App\GUI\domainBridge;



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