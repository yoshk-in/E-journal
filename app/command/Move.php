<?php


namespace App\command;


use App\base\AbstractRequest;
use App\domain\Product;
use App\domain\ProductMap;
use App\repository\ProductRepository;
use Psr\Container\ContainerInterface;

abstract class Move extends Command
{

    protected $productRepository;
    protected $orm;
    const ERR_NOT_ARRIVED = 'данные блоки еше не поступали на настройку:';
    private $container;
    private $productMap;


    public function __construct(ProductRepository $repository, AbstractRequest $request, ContainerInterface $container, ProductMap $productMap)
    {
        $this->productRepository = $repository;
        parent::__construct($request);
        $this->container = $container;
        $this->productMap = $productMap;
    }

    public function execute()
    {
        Product::setNumberStrategy($this->container->get($this->productMap->getNumberStrategy($this->request->getProduct())));
        $this->doExecute(...$this->getRequestProps());
    }

    protected function getRequestProps(): array
    {
        $product_name = $this->request->getProduct();
        $numbers = $this->request->getBlockNumbers();
        $special_command = $this->request->getPartial();
        return [$product_name, $numbers, $special_command];
    }


    abstract protected function doExecute(
        string $productName,
        array $numbers,
        ?string $procedure
    );

}