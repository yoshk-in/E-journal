<?php


namespace App\command;

use App\repository\DBSchemaManager;


class ClearJournalCommand
{
    protected $dbManager;

    public function __construct(DBSchemaManager $dbManager)
    {
        $this->dbManager = $dbManager;
    }

    public function execute() {
         $this->dbManager->updateTableSchema();
     }

}

