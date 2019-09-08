<?php

namespace App\command;

use App\base\exceptions\IncorrectInputException;
use App\base\ConsoleRequest;
use App\domain\ProcedureMap;
use App\domain\ProductRepository;
use \ArrayAccess;

abstract class Command
{
    protected $request;
    protected $repository;
    protected $productMap;

    final public function __construct(
        ConsoleRequest $request,
        ProductRepository $repository,
        ProcedureMap $productMap
    )  {
        $this->request = $request;
        $this->repository = $repository;
        $this->productMap = $productMap;
    }

    public function execute(string $domainClass): array
    {
        $product_name = $this->request->getProductName();
        $numbers = $this->request->getBlockNumbers();
        $procedure_map = $this->productMap->getProcedures($product_name);
        [$found_collection, $not_found_array] =
            $this->repository->findByNumbers($domainClass, $product_name, count($procedure_map), $numbers);
        $command = $this->request()->getPartialProcCommand();
        $output = $this->doExecute(
            $found_collection,
            $this->repository,
            $domainClass,
            $product_name,
            $not_found_array,
            $command
        );
        $this->repository->save();
        echo static::class . "\n";
        return $output;
    }


    protected function request(): ConsoleRequest
    {
        return $this->request;
    }

    protected function ensureRightInput(bool $condition, string $msg = '', ?array $numbers = null)
    {
        $numb_str = '';
        if ($numbers) foreach ($numbers as $number) $numb_str .= $number . "\n";
        if (!$condition) throw new IncorrectInputException("неверно заданы параметры запроса: $msg\n $numb_str");
    }

    protected function getCommonInfo($output): array
    {
        return ['отмечены следующие события: ', $output ?? null];
    }

    abstract protected function doExecute(
        ArrayAccess $collection,
        ProductRepository $repository,
        string $domainClass,
        string $productName,
        array $not_found_numbers,
        ?string $procedure
    ): array;

}

