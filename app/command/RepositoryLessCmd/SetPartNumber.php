<?php

namespace App\command\RepositoryLessCmd;


use App\base\CLIRequest;
use App\cache\Cache;
use App\command\Command;

class SetPartNumber extends Command
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
        $productName = $this->request->getProductName();
        $partNumber = $this->request->getParty();
        $this->cache->setPartNumber($productName, $partNumber);
        echo $productName . ' установлен номер партии ' . $partNumber;
        exit;
    }
}
