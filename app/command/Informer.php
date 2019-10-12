<?php


namespace App\command;


use App\CLI\render\InfoManager;
use App\domain\ProcedureMap;
use App\repository\ProductRepository;

abstract class Informer extends Move
{
    protected $render;

    public function __construct(
        ProductRepository $repository,
        ProcedureMap $productMap,
        InfoManager $render
    )  {
        parent::__construct($repository, $productMap);
        $this->render = $render;

    }

    abstract protected function doExecute(
        $productName,
        $numbers,
        $procedure
    );
}