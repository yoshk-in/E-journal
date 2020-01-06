<?php


namespace App\domain;



class ProductFactory
{
    private ProductMap$map;
    private ProcedureFactory $procedureFactory;

    public function __construct(ProductMap $map, ProcedureFactory $procedureFactory)
    {
        $this->map = $map;
        $this->procedureFactory = $procedureFactory;
    }

    public function create(string $productClass, string $product, ?int $number)
    {
        return new $productClass($number, $product, $this->procedureFactory);
    }

}