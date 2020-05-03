<?php


namespace App\domain\procedures\data;


use A\B;
use App\domain\procedures\CasualProcedure;
use App\domain\procedures\CompositeProcedure;
use App\domain\procedures\executionStrategy\ProcedureExecutionStrategy;
use App\domain\procedures\interfaces\ProcedureInterface;
use App\domain\procedures\decorators\OwnerDecorator;
use App\domain\procedures\traits\IProcedureOwner;
use App\domain\AbstractProduct;

class AbstractProcedureData
{
    protected string $name;
    protected int $productOrder;
    protected int $ownerOrder;
    protected CasualProcedure $initializingProcedure;
    protected \stdClass $executionStrategyData;

    protected static \stdClass $ownerData;
    protected static int $productCounter = 0;



    const CREATE_DATA_MAP = [
        AbstractProcedureData::class => CasualProcedure::class,
        CompositeProcedureData::class => CompositeProcedure::class,
    ];





    public function __construct(string $name, \stdClass $executionStrategyData)
    {
        $this->name = $name;
        $this->productOrder = ++self::$productCounter;
        $this->ownerOrder = ++(self::$ownerData)->counter;
        $this->executionStrategyData = $executionStrategyData;
    }

    public static function resetProductCounter()
    {
        self::$productCounter = 0;
    }


    public function getInterval(): string
    {
        return $this->executionStrategyData->interval;
    }


    public function create(): ProcedureInterface
    {
        $procedure = self::CREATE_DATA_MAP[static::class];
        $this->initializingProcedure = new $procedure($this);
        return $this->initializingProcedure->getFacade();
    }


    /**
     * @param IProcedureOwner $owner
     * @param string|OwnerDecorator $ownerStrategy
     */
    public static function setOwnerData(IProcedureOwner $owner, string $ownerStrategy): void
    {
        $ownerData = new \stdClass();
        $ownerData->owner = $owner;
        $ownerData->strategy = $ownerStrategy;
        $ownerData->counter = 0;
        self::$ownerData = $ownerData;
    }


    public function getInitializingProcedure(): CasualProcedure
    {
        return $this->initializingProcedure;
    }

    protected function createOwnerStrategy(): OwnerDecorator
    {
        return ((self::$ownerData)->strategy)::create((self::$ownerData)->owner, $this);
    }

    protected function createExecutionStrategy(): ProcedureExecutionStrategy
    {
        $class = $this->executionStrategyData->strategy;
        return new $class($this);
    }

    public function initProcedureData(CasualProcedure $initializingProc): array
    {
        $this->initializingProcedure = $initializingProc;
        return [$this->productOrder, $this->createExecutionStrategy(), $this->createOwnerStrategy()];
    }

    public function getOwnerStrategyData(): array
    {
        return [$this->initializingProcedure, $this->name, $this->ownerOrder];
    }


}