<?php


namespace App\domain;


use App\base\AppMsg;
use App\events\ISubscriber;
use App\repository\DBLayer;

/** @Entity */

class ProductMonthlyCounter implements ISubscriber
{
    /** @Column(type="array") */
    private $lastStartedNumber = [];

    /** @Column(type="array") */
    private $monthlyCount = [];

    /** @Id @Column(type="integer") */
    private static $id = 1;

    private $events = [];
    private static $dbLayer;

    const EVENT = AppMsg::PRODUCT_STARTED;

    private function __construct()
    {
    }

    public static function create(DBLayer $dbLayer): self
    {
        self::$dbLayer = $dbLayer;
        if (!($self = $dbLayer->findEntityById(self::class, self::$id))) {
           $self = new self;
        }
        self::$dbLayer->persist($self);
        return $self;
    }

    public static function creatAndAttachCountableProduct(DBLayer $DBLayer, ProductMap $map): self
    {
        $self = self::create($DBLayer);
        foreach ($map->getCountableProducts() as $product) {
            $self->attachProduct($product);
        }
        return $self;
    }

    public function isCountable(string $product): bool
    {
        return isset($this->monthlyCount[$product]);
    }


    public function attachProduct(string $name)
    {
        $this->events[] = $name . self::EVENT;
    }

    public function update($observable, string $event)
    {
        $this->count($observable);
    }

    public function subscribeOn(): array
    {
        return $this->events;
    }

    public function changeMonthlyCount(string $product, int $count)
    {
        if (!isset($this->monthlyCount[$product])) return;
        $this->monthlyCount[$product] = $count;
    }

    public function getMonthlyCount(string $product): int
    {
        return $this->monthlyCount[$product] ?? 0;
    }

    public function getLastProductNumber(string $product): ?int
    {
        return $this->lastStartedNumber[$product]?? null;
    }

    private function count(Product $product)
    {
        $name = $product->getName();
        $number = $product->getNumber();
        $number < ($this->lastStartedNumber[$name] ?? 0) ?: $this->lastStartedNumber[$name] = $number;
        isset($this->monthlyCount[$name]) ? ++$this->monthlyCount[$name] : $this->monthlyCount[$name] = 1;
    }

    public function __destruct()
    {
        self::$dbLayer->save();
    }
}