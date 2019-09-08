<?php


namespace App\domain;

use Exception;
use ReflectionClass;
use Doctrine\Common\Collections\Collection;

class ProductRepository
{
    private $productMap;
    private $orm;
    private $domainClass = Product::class;

    public function __construct(ProcedureMap $productMap, ORM $orm)
    {
        $this->orm = $orm;
        $this->productMap = $productMap;
    }

    public function createProducts(array $numbers, string $domainClass, string $productName): array
    {
        foreach ($numbers as $number) {
            $object = new $domainClass($number, $productName, $this->productMap->getProcedures($productName));
            $objects[] = $object;
            $this->orm->persist($object);
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
        $product_name_criteria = $this->orm->andCriteria($name_field, $productName);
        return $result = !is_null($numbers) ?
            $this->orm->findConcreteProducts($product_name_criteria, $id_field, $numbers) :
            [
                $found_collection = $this->orm->findNotFinishedProducts(
                    $product_name_criteria,
                    $current_proc_id_field,
                    $maxProcedureCount
                ),
                $not_found = null
            ];
    }

    public function save()
    {
        $this->orm->save();
    }

    protected function getProductTableData(string $product): array
    {
        $reflection = new ReflectionClass($product);
        $props = array_keys($reflection->getDefaultProperties());
        $required_props = ['currentProc', 'number', 'name'];

        array_map(function ($prop) use ($props) {
            if (array_search($prop, $props) === false)
                throw new Exception('table cache and class properties must be same');
        }, $required_props);

        return $required_props;
    }

}