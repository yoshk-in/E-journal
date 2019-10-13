<?php


namespace App\repository;


use App\domain\PartialProcedure;
use App\domain\Procedure;
use App\domain\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

class DBSchemaManager
{
    protected $dbTool;

    private $servicedDatabaseClasses = [
        Product::class,
        Procedure::class,
        PartialProcedure::class
    ];

    private $em;


    public function __construct(EntityManagerInterface $em, SchemaTool $tool)
    {
        $this->dbTool = $tool;
        $this->em = $em;
    }

    public function updateTableSchema()
    {
        $this->dbTool->dropSchema($this->servicedDatabaseClasses);
        $this->dbTool->createSchema($this->servicedDatabaseClasses);
    }



}