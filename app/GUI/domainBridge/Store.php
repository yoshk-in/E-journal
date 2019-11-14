<?php


namespace App\GUI\domainBridge;



class Store
{
    private $store = [];

    public function add(int $id, $data)
    {
        $this->store[$id] = $data;
    }

    public function get(int $id)
    {
        return $this->store[$id];
    }

    public function pop(int $id)
    {
        $data = $this->get($id);
        unset($this->store[$id]);
        return $data;
    }
}