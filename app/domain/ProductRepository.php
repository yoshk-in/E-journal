<?php


namespace App\domain;

use Doctrine\Common\Collections\Criteria;
use Exception;
use ReflectionClass;


class ProductRepository
{
    private $productMap;
    private $orm;
    private $domainClass = Product::class;

    private const FIELDS = [
        'nameAndNumber'     => 1,
        'finished'          => 2,
        'finishedAndName' => 3,
    ];

    public function __construct(ProcedureMapManager $productMap, ORM $orm)
    {
        $this->orm = $orm;
        $this->productMap = $productMap;
    }

    public function createProducts(array $numbers, string $productName): array
    {
        foreach ($numbers as $number) {
            $object = new $this->domainClass($number, $productName, $this->productMap->getProductProcedures($productName));
            $objects[] = $object;
            $this->orm->persist($object);
        }
        return $objects;
    }

    public function findByNumbers( string $productName, array $numbers): array
    {
        [$number, $name] = $this->getProductTableFields(self::FIELDS['nameAndNumber']);
        $found = $this->orm->findWhereEach($this->orm->whereCriteria($name, $productName), $number, $numbers, 'or');
        $not_found = array_filter($numbers, function ($number) use ($found) {
            foreach ($found as $product) if ($product->getNumber() === $number) return false;
            return true;
        });
        return [$found, $not_found];
    }

    public function findNotFinished(string $productName) : \ArrayAccess
    {
        [$finished, $name] = $this->getProductTableFields(self::FIELDS['finishedAndName']);
        return $this->orm->findWhereEach($this->orm->whereCriteria($name, $productName), $finished, [false], 'and');
    }

    public function save()
    {
        $this->orm->save();
    }

    protected function getProductTableFields(?string $field = null): array
    {
        $reflection = new ReflectionClass($this->domainClass);
        $props = array_keys($reflection->getDefaultProperties());
        $required_props = ['finished', 'number', 'name'];

        array_map(function ($prop) use ($props) {
            if (array_search($prop, $props) === false)
                throw new Exception('table cache and class properties must be same');
        }, $required_props);

        switch ($field) {
            case self::FIELDS['nameAndNumber']:     return [$required_props[1],$required_props[2]];
            case self::FIELDS['finished']:          return [$required_props[0]];
            case self::FIELDS['finishedAndName']:   return [$required_props[0], $required_props[2]];
            default :                               return $required_props;
        }
    }
}