<?php


namespace App\repository;


use App\base\AppMsg;
use App\base\exceptions\WrongInputException;
use App\domain\AbstractProcedure;
use App\domain\Product;
use App\domain\ProductFactory;
use App\events\Event;
use App\events\ISubscriber;

class ProductRepository implements ISubscriber
{

    private DBLayer $orm;
    private string $domainClass = Product::class;

    const FIELD = [
        'name' => 'name',
        'number' => 'number',
        'finished' => 'finished',
        'started' => 'started'
    ];

    const NAME_FIELD = 'name';
    const NUMBER_FIELD = 'number';
    const FINISHED_FIELD = 'finished';
    const STARTED_FIELD = 'started';
    const ADVANCED_FIELD = 'advancedNumber';

    const SUBSCRIBE_ON = [
        Event::PROCEDURE_CHANGE_STATE,
        Event::PRODUCT_STARTED,
        Event::PRODUCT_CHANGE_STATE,
        Event::PERSIST_NEW
    ];

    private ProductFactory $pFactory;

    public function __construct(DBLayer $orm, ProductFactory $pFactory)
    {
        $this->orm = $orm;
        $this->orm->setServicedEntity($this->domainClass);
        $this->checkMetadataDomainClass();
        $this->pFactory = $pFactory;
    }

    private function checkMetadataDomainClass()
    {
        $reflection = $reflection = new \ReflectionClass($this->domainClass);
        $props = array_keys($reflection->getDefaultProperties());
        foreach (self::FIELD as $require) {
            if (!in_array($require, $props))
                throw new \Exception('table cache and class properties must be same');
        }
    }

    public function createProducts(array $numbers, string $productName): \Iterator
    {
        [$found, $not_found] = $this->findByNumbers($productName, $numbers);
        if (!empty($found)) throw new WrongInputException('передан на создание номер, о котором уже существует запись в журнале');
        foreach ($not_found as $number) {
            yield $this->pFactory->create($this->domainClass, $productName, $number);
        }
    }

    public function findByNumbers(string $productName, array $numbers): array
    {
        return $this->orm->findWhere(
            [self::NAME_FIELD => $productName, self::NUMBER_FIELD => $numbers],
            [self::NUMBER_FIELD => 'ASC']
        );
    }

    public function findUnfinishedByAdvancedNumber(string $productName, $advancedNumber): array
    {
        return $this->orm->findWhere([self::NAME_FIELD => $productName, self::FINISHED_FIELD => false, self::ADVANCED_FIELD => $advancedNumber], [self::ADVANCED_FIELD => 'ASC']);
    }

    public function findUnfinished(string $productName)
    {
        return $this->orm->findWhere([self::NAME_FIELD => $productName, self::FINISHED_FIELD => false], [self::NUMBER_FIELD => 'ASC']);
    }

    public function findLast(string $productName): ?Product
    {
        return $this->orm->findOneWhere([self::NAME_FIELD => $productName], [self::NUMBER_FIELD => 'DESC']);
    }

    public function findLastUnfinished(string $product): ?Product
    {
        return $this->orm->findOneWhere([self::NAME_FIELD => $product, self::FINISHED_FIELD => false], [self::NUMBER_FIELD => 'DESC']);
    }

    public function findFirstUnfinished(string $product): ?Product
    {
        return $this->orm->findOneWhere([self::NAME_FIELD => $product, self::FINISHED_FIELD => false], [self::NUMBER_FIELD => 'ASC']);
    }

    public function findStartedAndUnfinished(string $product): array
    {
        return $this->orm->findWhere([self::NAME_FIELD => $product, self::FINISHED_FIELD => false, self::STARTED_FIELD => true], [self::NUMBER_FIELD => 'ASC']);
    }

    public function save()
    {
        $this->orm->save();
    }

    public function update($observable, string $event)
    {
        $this->orm->persist($observable);
    }

    public function subscribeOn(): array
    {
        return self::SUBSCRIBE_ON;
    }


    public function findAll(string $productName): array
    {
        return $this->orm->findAll([self::NAME_FIELD => $productName], [self::NUMBER_FIELD => 'ASC']);
    }


}