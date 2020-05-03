<?php


namespace App\domain\procedures\factories;


use App\domain\procedures\CasualProcedure;
use App\domain\procedures\CompositeProcedure;
use App\domain\procedures\data\AbstractProcedureData;
use App\domain\procedures\data\CompositeProcedureData;
use App\domain\procedures\executionStrategy\AutoProcedureExecutionStrategy;
use App\domain\procedures\executionStrategy\ManualProcedureExecutionStrategy;
use App\domain\procedures\interfaces\ProcedureInterface;
use App\domain\procedures\ProcedureMap;
use App\domain\procedures\traits\IProcedureOwner;
use App\domain\AbstractProduct;
use Generator;

class ProcedureFactory implements IProductProcedureFactory
{
    protected ProcedureMap $creationMap;

    const COMPOSITE_ClASS = CompositeProcedureData::class;
    const COMPOSITE_MARK = 'inners';


    const MANUAL_EXEC_STRATEGY = ManualProcedureExecutionStrategy::class;
    const AUTO_EXEC_STRATEGY = AutoProcedureExecutionStrategy::class;
    const AUTO_EXEC_MARK = 'interval';


    /** @var AbstractProcedureData|string */
    protected static string $casualClass = AbstractProcedureData::class;


    public function __construct(ProcedureMap $creationMap)
    {
        $this->creationMap = $creationMap;
    }


    /**
     * @param IProcedureOwner|AbstractProduct $owner
     * @return Generator
     * @return CasualProcedure[]|CompositeProcedure[]
     */
    public function create(IProcedureOwner $owner): Generator
    {
        yield from $this->createProcedures($owner);
    }


    public function createProcedures(IProcedureOwner $owner): Generator
    {
        $procedures = $this->getProcedures($owner);
        foreach ($procedures as $name => $props) {
            yield $this->createOne($name, $props);
        }
    }


    protected function getProcedures(IProcedureOwner $product): array
    {
        return $this->creationMap->getProcedures();
    }


    protected function createOne(string $name, array &$props): ProcedureInterface
    {
        $class = isset($props[self::COMPOSITE_MARK]) ? self::COMPOSITE_ClASS : self::$casualClass;
        $execution_strategy = $this->getExecutionStrategy($props);

        /** @var AbstractProcedureData $data */
        $data = (new $class($name, $execution_strategy));
        return $data->create();
    }

    protected function getExecutionStrategy(array &$props): \stdClass
    {
        $strategyData = new \stdClass();
        if ($interval = $props[self::AUTO_EXEC_MARK] ?? false) {
            $strategyData->strategy = self::AUTO_EXEC_STRATEGY;
            $strategyData->interval = $interval;
        }
        $strategyData->strategy = self::MANUAL_EXEC_STRATEGY;
        return $strategyData;
    }


}