<?php


namespace App\command;


use App\base\AbstractRequest;
use App\domain\ProcedureMap;
use App\repository\ProductRepository;

abstract class Move extends Command
{

    protected $productRepository;
    protected $productMap;
    protected $orm;
    const ERR_NOT_ARRIVED = 'данные блоки еше не поступали на настройку:';


    public function __construct(ProductRepository $repository, ProcedureMap $productMap, AbstractRequest $request)
    {
        $this->productRepository = $repository;
        $this->productMap = $productMap;
        parent::__construct($request);
    }

    public function execute()
    {
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


    abstract protected function doExecute(
        string $productName,
        array $numbers,
        ?string $procedure
    );

}