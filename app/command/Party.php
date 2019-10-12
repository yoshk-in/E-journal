<?php

namespace App\command;


use App\base\CLIRequest;
use App\cache\Cache;
use App\domain\ProcedureMap;
use App\repository\ProductRepository;

class Party extends Command
{
    private $cache;
    private $request;

    public function __construct(CLIRequest $request, Cache $cache)
    {
        $this->cache = $cache;
        $this->request = $request;
    }

    public function execute()
    {
        $productName = $this->request->getProductName();
        $partNumber = $this->request->getPartNumber();
        $this->cache->setPartNumber($productName, $partNumber);
        echo $productName . ' установлен номер партии ' . $partNumber;
        exit;
    }
}
