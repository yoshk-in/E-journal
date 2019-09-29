<?php


namespace App\command;


use App\base\ConsoleRequest;
use App\cache\Cache;
use App\domain\ProcedureMapManager;
use App\repository\DBSchemaManager;
use App\repository\ProductRepository;

class ClearJournalCommand extends Command
{
    protected $dbManager;

    public function __construct(ConsoleRequest $request, ProductRepository $repository, ProcedureMapManager $productMap, Cache $cache, DBSchemaManager $dbManager)
    {
        parent::__construct($request, $repository, $productMap, $cache);
        $this->dbManager = $dbManager;
    }

    protected function doExecute(
         $productName,
         $numbers,
         $procedure
    ) {
         $this->dbManager->updateTableSchema();

     }

}

