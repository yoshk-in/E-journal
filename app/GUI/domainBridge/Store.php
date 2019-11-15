<?php


namespace App\GUI\domainBridge;



use App\GUI\CellRow;

class Store
{
    private $rowStore = [];
    private $productStore = [];

    public function add(int $id, $row)
    {
        $this->rowStore[$id] = $row;
        $this->addStartedProduct($row);
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

    public function getStartedProducts(): array
    {
        return $this->productStore;
    }

    private function addStartedProduct(CellRow $row)
    {
        if (!$row->getData()->isStarted()) return;
        $this->productStore[] = $row->getData();
    }
}