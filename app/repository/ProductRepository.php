<?php


namespace App\repository;


use App\base\AppMsg;
use App\domain\CompositeProcedure;
use App\domain\ProcedureFactory;
use App\domain\Product;
use App\events\Event;
use App\events\ISubscriber;
use Exception;
use ReflectionClass;

class ProductRepository implements ISubscriber
{

    private $orm;
    private $domainClass = Product::class;

    const FIELD = [
        'name' => 'name',
        'number' => 'number',
        'finished' => 'finished'
    ];

    const SUBSCRIBE_ON = [
        AppMsg::ARRIVE,
        AppMsg::DISPATCH,
        Event::PRODUCT_MOVE
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
            foreach ($object->getProcedures() as $proc)
            {
                $this->orm->persist($proc);
                if ($proc instanceof CompositeProcedure) {
                    foreach ($proc->getInners() as $inner) {
                        $this->orm->persist($inner);
                    }
                }
            }
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

    public function update(Object $observable, string $event)
    {
        $this->orm->persist($observable);
    }

    public function subscribeOn(): array
    {
        return self::SUBSCRIBE_ON;
    }
}