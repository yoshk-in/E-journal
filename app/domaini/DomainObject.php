<?php

namespace App\domaini;

use App\base\AppException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class DomainObject
{
    /**
     * @Id
     * @Column(type="integer")
     **/
    protected $number;
    protected $procedureCollection;
    protected $TTCollection;
    protected $compositeProcedures;
    protected static $PROCEDURES;
    protected static $TECHNICAL_PROCEDURES_REGULATIONS;
    protected static $PROCEDURES_REGULATIONS;
    protected $currentProcedureId;

    public function __construct()
    {
        $this->procedureCollection = new ArrayCollection();
        $this->TTCollection = new ArrayCollection();
        $msg = 'some constants is not defined in child class: ';
        $this->ensure(!is_null(static::$PROCEDURES), $msg . 'PROCEDURES are not defined');
        $this->ensure(!is_null(static::$TECHNICAL_PROCEDURES_REGULATIONS), $msg . 'TECH_PROC_REG are not defined');
        $this->ensure(!is_null(static::$PROCEDURES_REGULATIONS), $msg . 'PROC_REG are not defined');
        $this->ensure(!is_null($this->compositeProcedures), 'prop compositeProcedure is required');
        $this->ensure(is_array($this->compositeProcedures), 'compositeProcedure must be array');
    }

    public function initByNumber(int $number): void
    {
        //procedures are initialized?
        $this->proceduresAreInitialized();
        $this->number = $number;
        $this->initProcedures();
        $this->currentProcedureId = 0;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function ensure($conditions, string $msg = null)
    {
        if (is_array($conditions)) {
            foreach ($conditions as $condition)
                if (!$condition) throw new AppException('ошибка: операция не выполнена ' . $msg);
        } else {
            if (!$conditions) throw new AppException('ошибка: операция не выполнена ' . $msg);
        }
    }

    public function startProcedure()
    {
        $currentProcedure = $this->getCurrentProcedureByStageId();
        $this->checkLastProcedureEnd();
        $this->ensure(is_null($currentProcedure->getStart()), ' - начало данной процедуры уже отмечено');
        $currentProcedure->setStart();
    }

    public function endProcedure()
    {
        $currentProcedure = $this->getCurrentProcedureByStageId();
        $this->checkStartAndEndProcedure($currentProcedure);
        if ($this->isCompositeProcedure($currentProcedure->getName()))
            $this->checkCompositeProcedureIsFinished($this->TTCollection, static::$TECHNICAL_PROCEDURES_REGULATIONS);
        $currentProcedure->setEnd();
        $nextId = $currentProcedure->getIdStage();
        $this->currentProcedureId[++$nextId];
    }

    protected function proceduresAreInitialized()
    {
        $alreadyInit = $this->procedureCollection->first();
        $this->ensure($alreadyInit,'procedures already are initialized');

    }

    protected function initProcedures()
    {
        foreach (static::$PROCEDURES as $key => $procedure) {
            $this->procedureCollection->add(new Procedure());
            $this->procedureCollection[$key]->setIdentityData($procedure, $this, $key);
        }
        $index = 0;
        foreach (static::$TECHNICAL_PROCEDURES_REGULATIONS as $techProcedure => $time) {
            $this->TTCollection->add(new TechnicalProcedure());
            $this->TTCollection[$index]->setIdentityData($techProcedure, $this, $key);
            ++$index;
        }
    }

    protected function getCurrentProcedureByStageId() : Procedure
    {
        return $this->procedureCollection[$this->currentProcedureId];
    }

    protected function isCompositeProcedure(string $name)
    {
        if (in_array($name, $this->compositeProcedures)) return true;
        return false;
    }

    protected function checkStartAndEndProcedure(Procedure $procedure) : void
    {
        $this->ensure(!is_null($procedure->getStart()), ' - начало данной процедуры не отмечено');
        $this->ensure(is_null($procedure->getEnd()), ' - окончание данной процедуры уже отмечено');
    }

    protected function checkLastProcedureEnd() : void
    {
        if ($this->currentProcedureId !== 0) {
            $last = $this->procedureCollection[--$this->currentProcedureId];
            $this->ensure(!is_null($last->getEnd()), 'окончание прошлой процедуры еше не отмечено');
        }
    }


    abstract protected function checkCompositeProcedureIsFinished(Collection $collection, array $arrayOfComposite) : void ;


}


