<?php

namespace App\command\casual;


use App\base\CLIRequest;
use App\cache\Cache;
use App\command\Command;

class Party extends Command
{
    private Cache $cache;
    private CLIRequest $request;

    public function __construct(CLIRequest $request, Cache $cache)
    {
        $this->cache = $cache;
        $this->request = $request;
    }

    public function execute()
    {
        $productName = $this->request->getProduct();
        $partNumber = $this->request->getParty();
        $this->cache->setPartNumber($productName, $partNumber);
        echo $productName . ' установлен номер партии ' . $partNumber;
        exit;
    }
}
