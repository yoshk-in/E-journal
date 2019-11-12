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
use App\domain\ProductNumberManager;

class ProductRepository implements ISubscriber
{

    private $orm;
    private $domainClass = Product::class;
    private $numbersHorizon = ProductNumberManager::class;

    const FIELD = [
        'name' => 'name',
        'number' => 'number',
        'finished' => 'finished'
    ];

    const NAME_FIELD = 'name';
    const NUMBER_FIELD = 'number';
    const FINISHED_FIELD = 'finished';

    const SUBSCRIBE_ON = [
        AppMsg::ARRIVE,
        AppMsg::DISPATCH,
        Event::PRODUCT_MOVE,
    ];

    private $procedureFactory;

    public function __construct(DoctrineORMAdapter $orm, ProcedureFactory $procedureFactory)
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
        [,$not_found] = $this->findByNumbers($productName, $numbers);
        foreach ($not_found as $number) {
            $object = new $this->domainClass($number, $productName, $this->procedureFactory);
            $objects[] = $object;
            $this->orm->persist($object);
            $this->persistProductProcedures($object);
        }
        return $objects ?? [];
    }

    public function findByNumbers(string $productName, array $numbers): array
    {
        $found = $this->orm->findWhere(
            [self::NAME_FIELD => $productName, self::NUMBER_FIELD => $numbers],
            [self::NUMBER_FIELD => 'ASC']
        );

        $not_found = array_filter($numbers, function ($number) use ($found) {
            foreach ($found as $product) {
                if ($product->getNumber() == $number) return false;
            }
            return true;
        });

        return [$found, $not_found];
    }

    public function findUnfinished(string $productName): array
    {
        return $this->orm->findWhere([self::NAME_FIELD => $productName, self::FINISHED_FIELD => false], [self::NUMBER_FIELD => 'ASC']);
    }

    public function findLast(string $productName): ?Product
    {
        return $this->orm->findOneWhere([self::NAME_FIELD => $productName], [self::NUMBER_FIELD => 'DESC']);
    }

    public function findLastUnfinished(string $product): ?Product
    {
        return $this->orm->findOneWhere([self::NAME_FIELD => $product, self::FINISHED_FIELD => false], [self::NAME_FIELD => 'DESC']);
    }

    public function findFirstUnfinished(string $product): ?Product
    {
        return $this->orm->findOneWhere([self::NAME_FIELD => $product, self::FINISHED_FIELD => false], [self::NAME_FIELD => 'ASC']);
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

    public function getNumbersMng(string $productName) : ProductNumberManager
    {
        $horizon = $this->orm->findEntityById($this->numbersHorizon, $productName);
        if (empty($horizon)) {
            $horizon = new ProductNumberManager();
            $horizon->setProductName($productName);
            $this->orm->persist($horizon);
            return $horizon;
        };
        return $horizon;
    }

    public function findAll(string $productName): array
    {
        return $this->orm->findAll([self::NAME_FIELD => $productName], [self::NUMBER_FIELD => 'ASC']);
    }

    private function persistProductProcedures(Product $product)
    {
        foreach ($product->getProcedures() as $proc) {
            $this->orm->persist($proc);
            if ($proc instanceof CompositeProcedure) {
                foreach ($proc->getInners() as $inner) {
                    $this->orm->persist($inner);
                }
            }
        }
    }
}