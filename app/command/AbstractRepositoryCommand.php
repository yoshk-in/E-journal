<?php


namespace App\command;


use App\base\AbstractRequest;
use App\domain\data\ProductData;
use App\domain\ProductMap;
use App\repository\ProductRepository;

abstract class AbstractRepositoryCommand extends Command
{

    protected ProductRepository $productRepository;
    protected AbstractRequest $request;


    public function __construct(ProductRepository $repository, AbstractRequest $request)
    {
        $this->productRepository = $repository;
        $this->request = $request;
    }

    public function execute()
    {
        $this->doExecute();
    }


    abstract protected function doExecute();

}