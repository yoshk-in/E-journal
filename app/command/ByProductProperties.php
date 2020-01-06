<?php


namespace App\command;


use App\base\AbstractRequest;
use App\domain\ProductMap;
use App\repository\ProductRepository;
use Psr\Container\ContainerInterface;

abstract class ByProductProperties extends Command
{

    protected ProductRepository $productRepository;
    const ERR_NOT_INIT = 'данные блоки еше не поступали на настройку:';
    private ContainerInterface $container;
    protected ProductMap $productMap;
    protected AbstractRequest $request;


    public function __construct(ProductRepository $repository, AbstractRequest $request, ContainerInterface $container, ProductMap $productMap)
    {
        $this->productRepository = $repository;
        $this->request = $request;
        $this->container = $container;
        $this->productMap = $productMap;
    }

    public function execute()
    {
        $this->doExecute(...$this->getRequestProps());
    }

    protected function getRequestProps(): array
    {
        $product_name = $this->request->getProduct();
        $numbers = $this->request->getBlockNumbers();
        $special_command = $this->request->getPartial();
        return [$product_name, $numbers, $special_command];
    }


    abstract protected function doExecute(string $productName, array $numbers, ?string $procedure);

}