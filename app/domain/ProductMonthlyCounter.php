<?php


namespace App\domain;


use App\domain\productManager\ProductClassManager;
use App\events\Event;
use App\events\IEvent;
use App\repository\DBLayer;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;


/** @Entity */

class ProductMonthlyCounter
{
    /** @Column(type="array") */
    private array $lastStartedNumber = [];

    /** @Column(type="array") */
    private array $monthlyCount = [];

    /** @Id @Column(type="integer") */
    private static int $id = 1;

    private array $events = [];
    private static DBLayer $dbLayer;
    private static ?self $self = null;

    const EVENT = IEvent::PRODUCT_STARTED;

    const COUNT = 'count';

    private function __construct()
    {
    }

    public static function create(DBLayer $dbLayer): self
    {
        if (self::$self) return self::$self;
        self::$dbLayer = $dbLayer;
        if (!($self = $dbLayer->findEntityById(self::class, self::$id))) {
           $self = new self;
        }
        self::$dbLayer->persist($self);
        return self::$self = $self;
    }

    public static function createAndAttachCountableProduct(DBLayer $DBLayer, ProductClassManager $map): self
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
        exit('todo');
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

    public function count(Event $eventOnProduct)
    {
        /** @var AbstractProduct $product */
        $product = $eventOnProduct->observable;
        $name = $product->getProductName();
        $number = $product->getProductNumber();
        $number < ($this->lastStartedNumber[$name] ?? 0) ?: $this->lastStartedNumber[$name] = $number;
        isset($this->monthlyCount[$name]) ? ++$this->monthlyCount[$name] : $this->monthlyCount[$name] = 1;
    }

    public function __destruct()
    {
        self::$dbLayer->save();
    }
}