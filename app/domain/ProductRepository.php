<?php


namespace App\domain;

use Exception;
use ReflectionClass;
use Doctrine\Common\Collections\Collection;

class ProductRepository
{
    use DoctrineORM{
        DoctrineORM::__construct as initDoctrine;
    }

    private $productMap;

    public function __construct(string $domainClass, ProcedureConfigurations $productMap, bool $devMode)
    {
        $this->initDoctrine($domainClass, $devMode);
        $this->productMap = $productMap;
    }

    public function createProducts(array $numbers, string $domainClass, string $productName): array
    {
        foreach ($numbers as $number) {
            $object = new $domainClass($number, $productName, $this->productMap->getProcedures($productName));
            $objects[] = $object;
            $this->persist($object);
        }
        return $objects;
    }

    public function findByNumbers(
        string $domainClass,
        string $productName,
        int $maxProcedureCount,
        ?array $numbers = null
    ): array
    {
        list($current_proc_id_field, $id_field, $name_field) = $this->getProductTableData($domainClass);
        $product_name_criteria = $this->ormAndCriteria($name_field, $productName);
        return $result = !is_null($numbers) ?
            $this->ormFindConcreteProducts($product_name_criteria, $id_field, $numbers) :
            [
                $found_collection = $this->ormFindNotFinishedProducts(
                    $product_name_criteria,
                    $current_proc_id_field,
                    $maxProcedureCount
                ),
                $not_found = null
            ];
    }

    protected function getProductTableData(string $product): array
    {
        $reflection = new ReflectionClass($product);
        $props = array_keys($reflection->getDefaultProperties());
        $required_props = ['currentProc', 'number', 'name'];

        array_map(function ($prop) use ($props) {
            if (array_search($prop, $props) === false)
                throw new Exception('table data and class properties must be same');
        }, $required_props);

        return $required_props;
    }

}