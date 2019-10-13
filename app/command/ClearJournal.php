<?php


namespace App\command;

use App\repository\DBSchemaManager;


class ClearJournal extends Command
{
    protected $dbManager;

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

