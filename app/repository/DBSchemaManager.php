<?php


namespace App\repository;


use App\domain\productManager\ProductClassManager;
use bootstrap\App;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Psr\Container\ContainerInterface;

class DBSchemaManager
{
    protected $dbTool;



    private $em;
    protected array $filesToDBService;
    protected ProductClassManager $productManager;


    public function __construct(EntityManagerInterface $em, SchemaTool $tool, ProductClassManager $productClassMng)
    {
        $this->dbTool = $tool;
        $this->em = $em;
        $pathsToService = ProductClassManager::ABSTRACT_PRODUCT_NAMESPACE;
        $this->productManager = $productClassMng;
        $this->getFilesToDBService($pathsToService);
    }

    public function updateTableSchema()
    {
        $this->productManager->generateProductClasses();
        $this->dbTool->dropSchema($this->filesToDBService);
        $this->dbTool->createSchema($this->filesToDBService);
    }



    protected function getFilesToDBService($pathsToService)
    {
        foreach ($pathsToService as $path) {
            !is_dir($path) ?: $this->getFilesToDBService($path);
            !is_file($path) ?: $this->filesToDBService[] = $path;
        }
    }


}