<?php

namespace App\command;

use App\base\exceptions\WrongInputException;
use App\base\ConsoleRequest;
use App\domain\ProcedureMapManager;
use App\domain\ProductRepository;
use \ArrayAccess;

abstract class Command
{
    protected $request;
    protected $productRepository;
    protected $productMap;
    const ERR = ['not_arrived' => 'данные блоки еше не поступали на настройку:'];

    final public function __construct(
        ConsoleRequest $request,
        ProductRepository $repository,
        ProcedureMapManager $productMap
    )  {
        $this->request = $request;
        $this->productRepository = $repository;
        $this->productMap = $productMap;
    }

    public function execute()
    {
        $product_name = $this->request->getProductName();
        $numbers = $this->request->getBlockNumbers();
        $special_command = $this->request->getPartialProcCommand();
        $this->doExecute(
            $this->productRepository,
            $product_name,
            $numbers,
            $special_command
        );
        $this->productRepository->save();
    }


    protected function request(): ConsoleRequest
    {
        return $this->request;
    }

    protected function ensureRightInput(bool $condition, string $msg = '', ?array $numbers = null)
    {
        $numb_str = '';
        if ($numbers) foreach ($numbers as $number) $numb_str .= $number . "\n";
        if (!$condition) throw new WrongInputException("неверно заданы параметры запроса: $msg\n $numb_str");
    }


    abstract protected function doExecute(
        ProductRepository $repository,
        string $productName,
        array $numbers,
        ?string $procedure
    );

}

