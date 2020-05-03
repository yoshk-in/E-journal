<?php

namespace App\domain;

use App\base\exceptions\ProcedureException;
use App\domain\data\AbstractProductData;
use App\domain\data\ProductData;
use App\domain\numberStrategy\SimpleNumberStrategy;
use App\domain\numberStrategy\DoubleNumberStrategy;
use App\domain\numberStrategy\NumberStrategy;
use App\domain\procedures\CasualProcedure;
use App\domain\procedures\interfaces\NameStateInterface;
use App\domain\procedures\interfaces\ProcedureInterface;
use App\domain\procedures\traits\IProcedureOwner;
use App\domain\traits\TProcedureCollectionOwner;
use App\domain\traits\TOwnerProduct;
use App\objectPrinter\TPrintingObject;
use App\repository\traits\TDatabase;
use Doctrine\ORM\Mapping\InheritanceType;
use App\events\{Event, traits\TObservable};
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

;


/**
 * @Entity
 * @InheritanceType("SINGLE_TABLE")
 */
abstract class AbstractProduct implements IProcedureOwner, NameStateInterface
{
    use TProcedureCollectionOwner, TOwnerProduct, TObservable, TDatabase, TPrintingObject;

    //** procedure handling marks */
    const FIRST_PROC = 0;
    const MIDDLE_PROC = null;
    const LAST_PROC = 2;

    /** @var callable[] */
    const PROCEDURE_HANDLERS_BY_MARK = [
        CasualProcedure::READY_TO_END => [
            self::FIRST_PROC => 'firstProcStartHandle',
            self::MIDDLE_PROC => 'procStartHandle',
            self::LAST_PROC => 'procStartHandle'
        ],
        CasualProcedure::ENDED => [
            self::FIRST_PROC => 'procEndHandle',
            self::MIDDLE_PROC => 'procEndHandle',
            self::LAST_PROC => 'lastProcEndHandle'
        ],
    ];


    /**
     * @Id
     * @Column(type="string", unique=true)
     */
    protected string $id;


    /** @Column(type="integer", nullable=true) */
    protected ?int $number;
    /** @Column(type="integer"), nullable=false */
    protected int $preNumber;

    /** @Column(type="integer") */
    protected int $numberStrategy;

    const INIT = NameStateInterface::READY_TO_START;
    const STARTED = NameStateInterface::READY_TO_END;

    /** @Column(type="integer", nullable=false) */
    protected int $state = self::INIT;

    /** @Column(type="string", nullable=false) */
    protected string $stateName;

    /** @Column(type="datetime_immutable", nullable=false) */
    protected \DateTimeInterface $start;
    /** @Column(type="datetime_immutable", nullable=true) */
    protected ?\DateTimeInterface $end = null;


    const NUMBER_STRATEGY_TO_STRING = [
        0 => SimpleNumberStrategy::class,
        1 => DoubleNumberStrategy::class
    ];

    const NUMBER_STRATEGY_TO_INT = [
        SimpleNumberStrategy::class => 0,
        DoubleNumberStrategy::class => 1
    ];


    public function __construct(ProductData $productData)
    {
        [$this->number, $this->preNumber, $this->id, $numberStrategy] = $productData->getData();
        $this->numberStrategy = self::NUMBER_STRATEGY_TO_INT[$numberStrategy];
        $this->createProcedures($productData);
        $this->start = new \DateTimeImmutable('now');
        $this->persist();
    }

    protected function createProcedures(ProductData $productData)
    {
        $this->finishedInners = new ArrayCollection();
        $this->innerProcedures = new ArrayCollection();
        $this->notFinishedInners = new ArrayCollection();
        foreach ($productData->createProcedures($this) as $procedure) {
            $this->innerProcedures->set($this->procedureKey($procedure), $procedure);
        }
        $this->innerProcedures->last()->setMark(self::LAST_PROC);
        $this->notFinishedInners = $this->innerProcedures;
        $this->processingInner = $this->innerProcedures->first()->setMark(self::FIRST_PROC);
        $this->stateName = $this->processingInner->getName();
    }


    /**
     * @return NumberStrategy
     */
    public function getNumberStrategy(): string
    {
        return self::NUMBER_STRATEGY_TO_STRING[$this->numberStrategy];
    }

    public function getProductName(): string
    {
        return static::NAME;
    }

    public function setStateName(string $name)
    {
        $this->stateName = $name;
    }


    public function procedureOwnerHandling(ProcedureInterface $procedure)
    {
        $handleFunc = self::PROCEDURE_HANDLERS_BY_MARK[$procedure->getState()][$procedure->getMark()];
        $this->$handleFunc($procedure);
        $this->persist();
    }


    protected function procedureKey(ProcedureInterface $procedure)
    {
        return $procedure->getName();
    }

    protected function firstProcStartHandle(ProcedureInterface $procedure)
    {
        $this->state = self::STARTED;
        $this->procStartHandle($procedure);
        $this->event(Event::START);
    }

    protected function procStartHandle(ProcedureInterface $procedure)
    {
        $this->stateName = $procedure->getName();
    }


    protected function setToEnded(ProcedureInterface $procedure)
    {
        $key = $this->procedureKey($procedure);
        $this->notFinishedInners->remove($key);
        $this->finishedInners->set($key, $procedure);
    }

    protected function lastProcEndHandle(ProcedureInterface $procedure)
    {
        $this->state = self::ENDED;
        $this->end = new \DateTimeImmutable('now');
        $this->event(Event::END);
    }

    protected function procEndHandle(ProcedureInterface $procedure)
    {
        $this->processingInner = $this->notFinishedInners->first();
        $this->setToEnded($procedure);
    }

    public function nextMainNumber(): ?int
    {
        return $this->getNumberStrategy()::nextMainNumber($this->number, $this->preNumber);
    }


    public function getPreNumber()
    {
        return $this->preNumber;
    }

    public function getAnyNumber(): int
    {
        return $this->getNumberStrategy()::getAnyNumber($this->number, $this->preNumber);
    }

    public function changeNumbers(int $number, int $advancedNumber)
    {
        [$this->number, $this->id] = AbstractProductData::changeNumberAndId($this, $number);
        Event::toDoEvent($this);
    }


    public function getProductNumber(): ?int
    {
        return $this->number;
    }

    public function force(): void
    {
        $this->processingInner->force();
    }

    public function start(?string $innerName = null)
    {
        $this->processingInner->start($innerName);

    }

    public function end(?string $innerName = null)
    {
        $this->processingInner->end($innerName);
    }


    public function getProductId(): string
    {
        return $this->id;
    }


    public function isEnded(): bool
    {
        return $this->state == self::ENDED;
    }

    public function isStarted(): bool
    {
        return $this->state = self::STARTED;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function getStateName(): string
    {
        return $this->stateName;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function getName(): string
    {
        return $this->getProductName();
    }


}


