<?php


namespace App\command;


use App\base\AbstractRequest;
use App\CLI\render\InfoManager;
use App\domain\ProcedureMap;
use App\repository\ProductRepository;

abstract class Informer extends Move
{
    protected $dispatcher;

    public function __construct(
        ProductRepository $repository,
        ProcedureMap $productMap,
        InfoManager $render,
        AbstractRequest $request
    )  {
        parent::__construct($repository, $productMap, $request);
        $this->dispatcher = $render;
    }

    abstract protected function doExecute(
        $productName,
        $numbers,
        $procedure
    );
}