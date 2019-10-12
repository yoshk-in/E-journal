<?php


namespace App\command;


use App\base\AbstractRequest;
use App\domain\ProcedureMap;
use App\repository\ProductRepository;

abstract class Move extends Command
{
    protected $request;
    protected $productRepository;
    protected $productMap;
    protected $orm;
    const ERR_NOT_ARRIVED = 'данные блоки еше не поступали на настройку:';


    public function __construct(ProductRepository $repository, ProcedureMap $productMap)
    {
        $this->productRepository = $repository;
        $this->productMap = $productMap;

    }

    public function execute(AbstractRequest $request)
    {
        $this->request = $request;
        $product_name = $this->request->getProduct();
        $numbers = $this->request->getBlockNumbers();
        $special_command = $this->request->getPartial();
        try {
            $this->doExecute(
                $product_name,
                $numbers,
                $special_command
            );
        } catch (\Exception $e) {
            $e->getMessage();
            exit;
        }

        $this->productRepository->save();
    }

    public function request()
    {
        return $this->request;
    }

    abstract protected function doExecute(
        string $productName,
        array $numbers,
        ?string $procedure
    );

}