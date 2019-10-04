<?php


namespace App\command;


use App\base\ConsoleRequest;
use App\domain\ProcedureMapManager;
use App\repository\ProductRepository;

abstract class RepositoryCommand extends Command
{
    protected $request;
    protected $productRepository;
    protected $productMap;
    protected $orm;
    const ERR_NOT_ARRIVED = 'данные блоки еше не поступали на настройку:';


    public function __construct(ConsoleRequest $request, ProductRepository $repository, ProcedureMapManager $productMap)
    {
        parent::__construct($request);
        $this->productRepository = $repository;
        $this->productMap = $productMap;

    }

    public function execute()
    {
        $product_name = $this->request->getProductName();
        $numbers = $this->request->getBlockNumbers();
        $special_command = $this->request->getPartialProcName();
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