<?php


namespace App\domain\procedures;


use App\domain\procedures\data\CompositeProcedureData;
use App\domain\procedures\interfaces\ProcedureInterface;
use App\domain\procedures\traits\IProcedureOwner;
use App\domain\procedures\traits\TCompositeProcedureOwner;
use App\domain\traits\TProcedureCollectionOwner;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\OrderBy;


/**
 * @Entity
 *
 */
class CompositeProcedure extends CasualProcedure implements IProcedureOwner
{
    use TProcedureCollectionOwner, TCompositeProcedureOwner;


    const CHANGE_STATE = [
        self::READY_TO_START => self::READY_TO_START_INNER,
        self::ENDED => self::ENDED
    ];

    
    const START_ACTION_BY_STATE = [
            self::READY_TO_START => 'selfStart',
            self::READY_TO_START_INNER => 'callInnerProc',
            self::READY_TO_END_INNER => 'callInnerProc',
            self::READY_TO_END => 'analyze',
            self::ENDED => 'analyze'
    ];

    
    const END_ACTION_BY_STATE = [
            self::READY_TO_START => 'analyze',
            self::READY_TO_START_INNER => 'callInnerProc',
            self::READY_TO_END_INNER => 'callInnerProc',
            self::READY_TO_END => 'selfEnd',
            self::ENDED => 'analyze'
    ];
    
    const FORCE_BY_STATE = [
        self::READY_TO_START => 'selfStart',
        self::READY_TO_START_INNER => 'callInnerProc',
        self::READY_TO_END_INNER => 'callInnerProc',
        self::READY_TO_END  => 'end',
        self::ENDED => 'analyze'
    ];

    const HANDLING_INNER_MAP = [
        self::READY_TO_START => 'analyze',
        self::READY_TO_START_INNER => 'innerStartHandle',
        self::READY_TO_END_INNER => 'analyze',
        self::READY_TO_END  => 'innerStartHandle',
        self::ENDED => 'innerEndHandle'
    ];

    static string $callInnerProcMethod;


    public function __construct(CompositeProcedureData $data)
    {
        $this->initBaseProps($data);
        $this->createInners($data);
        $this->persist();
    }

    protected function createInners(CompositeProcedureData $data)
    {
        $this->innerProcedures = new ArrayCollection();
        $this->finishedInners = new ArrayCollection();
        $this->notFinishedInners = new ArrayCollection();
        foreach ($data->createInners($this) as $inner) {
            $this->innerProcedures->set($inner->getOwnerOrder(), $inner);
            $this->notFinishedInners->set($inner->getName(), $inner);
        }
    }
    
    public function start(?string $innerName): CasualProcedure
    {
        self::$callInnerProcMethod = __FUNCTION__;
        return $this->selfOrInnerOperationByState($innerName, self::START_ACTION_BY_STATE);
    }
    
    public function end(?string $innerName): CasualProcedure
    {
        self::$callInnerProcMethod = __FUNCTION__;
        return $this->selfOrInnerOperationByState($innerName, self::END_ACTION_BY_STATE );
    }
    
    protected function selfOrInnerOperationByState(?string &$innerName, array $actionsByState)
    {
        $method = $actionsByState[$this->getState()] ?? $this->executionStrategy->checkCurrentState();
        return $this->$method($innerName);
    }
    
    protected function callInnerProc(?string &$innerName)
    {
        $this->getInnerByName($innerName)->{self::$callInnerProcMethod}($innerName);
    }

    public function procedureOwnerHandling(ProcedureInterface $procedure)
    {
        $handlingMethod = self::HANDLING_INNER_MAP[$procedure->getState()];
        $this->$handlingMethod($procedure);
        $this->persist();
    }

    public function innerStartHandle(ProcedureInterface $procedure)
    {
        $this->processingInner = $procedure;
        $this->state = self::READY_TO_END_INNER;
    }

    public function innerEndHandle(ProcedureInterface $procedure)
    {
        $this->processingInner = null;
        $this->notFinishedInners->remove($procedure->getName());
        $this->finishedInners->set($procedure->getName(), $procedure);
        $this->state = $this->innersEnded() ? self::READY_TO_END : self::READY_TO_START_INNER;
    }


    public function force()
    {
        $force_method = static::FORCE_BY_STATE[$this->getState()];
        $this->$force_method($this->getProcessingOrNextProc()->getName());
    }

    protected function stateAfterStart(): int
    {
        return self::READY_TO_START_INNER;
    }


}