<?php


namespace App\command\casual;

use App\repository\DBSchemaManager;
use App\command\Command;

class ClearJournal extends Command
{
    protected DBSchemaManager $dbManager;

    public function __construct(DBSchemaManager $dbManager)
    {
        $this->dbManager = $dbManager;
    }

    public function execute() {
//         $this->dbManager->updateTableSchema();
        echo 'функция заблокирована';
        exit;
     }

}

