<?php


namespace App\domain;



class ProductFactory
{
    private $map;
    private $procedureFactory;

    public function __construct(ProductMap $map, ProcedureFactory $procedureFactory)
    {
        $this->map = $map;
        $this->procedureFactory = $procedureFactory;
    }

    public function create(string $productClass, string $product, ?int $number)
    {
        $object = new $productClass($number, $product, $this->procedureFactory);
        return $object;
    }

}