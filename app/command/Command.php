<?php

namespace App\command;

use App\base\exceptions\IncorrectInputException;
use App\base\ConsoleRequest;
use App\domain\ProcedureConfigurations;
use App\domain\ProductRepository;
use \ArrayAccess;

abstract class Command
{
    protected $request;

    final public function __construct()
    {

    }

    public function execute(
        ConsoleRequest $request,
        ProductRepository $repository,
        string $domainClass,
        ProcedureConfigurations $productMap
    ) : array {
        $this->request = $request;
        $product_name = $request->getProductName();
        $numbers = $request->getBlockNumbers();
        $procedure_map = $productMap->getProcedures($product_name);
        [$found_collection, $not_found_array] =
            $repository->findByNumbers($domainClass, $product_name, count($procedure_map), $numbers);
        $command = $this->request()->getPartialProcCommand();
        $output = $this->doExecute($found_collection, $repository, $domainClass, $product_name, $not_found_array, $command);
        $repository->save();
        echo static::class . "\n";
        return $output;
    }



    protected function request() : ConsoleRequest
    {
        return $this->request;
    }

    protected function ensureRightInput(bool $condition, string $msg = '', ?array $numbers = null)
    {
        $numb_str = '';
        if ($numbers) foreach ($numbers as $number) $numb_str .=  $number . "\n";
        if (!$condition) throw new IncorrectInputException("неверно заданы параметры запроса: $msg\n $numb_str");
    }

    protected function getCommonInfo($output) : array
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
    ) : array;

}

