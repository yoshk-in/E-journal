<?php

namespace App\command;


use App\base\ConsoleRequest;
use App\cache\Cache;
use App\domain\ProcedureMapManager;
use App\repository\ProductRepository;

class SetPartNumberCommand extends RepositoryCommand
{
    private $cache;

    public function __construct(ConsoleRequest $request, Cache $cache)
    {
        parent::__construct($request);
        $this->cache = $cache;
    }

    public function execute()
    {
        $productName = $this->request()->getProductName();
        $partNumber = $this->request()->getPartNumber();
        $this->cache->setPartNumber($productName, $partNumber);
        echo $productName . ' установлен номер партии ' . $partNumber;
    }
}
