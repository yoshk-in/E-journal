<?php


namespace App\repository;


use App\domain\ProcedureFactory;
use Exception;
use ReflectionClass;
use App\domain\Product;

class ProductRepository
{

    private $orm;
    private $domainClass = Product::class;

    const FIELD = [
        'name' => 'name',
        'number' => 'number',
        'finished' => 'finished'
    ];

    private $procedureFactory;

    public function __construct( DoctrineORMAdapter $orm, ProcedureFactory $procedureFactory)
    {
        $this->orm = $orm;
        $this->orm->setServicedEntity($this->domainClass);
        $this->checkMetadataDomainClass();
        $this->procedureFactory = $procedureFactory;
    }

    private function checkMetadataDomainClass()
    {
        $reflection = $reflection = new ReflectionClass($this->domainClass);
        $props = array_keys($reflection->getDefaultProperties());
        foreach (self::FIELD as $require) {
            if (!in_array($require, $props))
                throw new Exception('table cache and class properties must be same');
        }
    }

    public function createProducts(array $numbers, string $productName): array
    {
        foreach ($numbers as $number) {
            $object = new $this->domainClass($number, $productName, $this->procedureFactory);
            $objects[] = $object;
            $this->orm->persist($object);
        }
        return $objects;
    }

    public function findByNumbers( string $productName, array $numbers): array
    {
        $name_criteria =  $this->orm->whereProperty(self::FIELD['name'], $productName);
        $found = $this->orm->findWhereEach($name_criteria, self::FIELD['number'], $numbers);

        $not_found = array_filter($numbers, function ($number) use ($found) {
            foreach ($found as $product) {
                if ($product->getNumber() == $number) return false;
            }
            return  true;
        });

        return [$found, $not_found];
    }

    public function findNotFinished(string $productName) : \ArrayAccess
    {
        $name_criteria =  $this->orm->whereProperty(self::FIELD['name'], $productName);
        return $this->orm->findWhereEach($name_criteria, self::FIELD['finished'], [false]);
    }

    public function save()
    {
        $this->orm->save();
    }

}