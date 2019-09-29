<?php

namespace App\command;

use App\base\exceptions\WrongInputException;
use App\base\ConsoleRequest;
use App\cache\Cache;
use App\domain\ProcedureMapManager;
use App\repository\DoctrineORMAdapter;
use App\repository\ProductRepository;


abstract class Command
{
    protected $request;
    protected $productRepository;
    protected $productMap;
    protected $cache;
    protected $orm;

    const ERR = ['not_arrived' => 'данные блоки еше не поступали на настройку:'];

    public function __construct(
        ConsoleRequest $request,
        ProductRepository $repository,
        ProcedureMapManager $productMap,
        Cache $cache
    )
    {
        $this->request = $request;
        $this->productRepository = $repository;
        $this->productMap = $productMap;
        $this->cache = $cache;
    }

    public function execute()
    {
        $product_name = $this->request->getProductName();
        $numbers = $this->request->getBlockNumbers();
        $special_command = $this->request->getPartialProcCommand();
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
        string $productName,
        array $numbers,
        ?string $procedure
    );

}

