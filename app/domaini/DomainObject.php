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
    protected $currentProcedure;
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

    public function setNumber(int $number): void
    {
        //procedures are initialized?
        $this->proceduresAreInitialized();
        $this->number = $number;
        $this->initProcedures();
        $this->currentProcedure = $this->procedureCollection->first();
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
        if ($this->currentProcedureId !== 0) {
            $last = $this->procedureCollection[--$this->currentProcedureId];
            $this->ensure(!is_null($last->getEnd()), 'окончание прошлой процедуры еше не отмечено');
        }
        $this->ensure(is_null($this->currentProcedure->getStart()), ' - начало данной процедуры уже отмечено');
        $this->currentProcedure->setStart();
    }

    public function endProcedure()
    {
        $this->ensure(!is_null($this->currentProcedure->getStart()), ' - начало данной процедуры не отмечено');
        $this->ensure(is_null($this->currentProcedure->getEnd()), ' - окончание данной процедуры уже отмечено');
        if ($this->isCompositeProcedure($this->currentProcedure->getName()))
            $this->compositeProcedureIsFinished($this->TTCollection, static::$TECHNICAL_PROCEDURES_REGULATIONS);
        $this->currentProcedure->setEnd();
        $nextId = $this->currentProcedure->getIdStage();
        $this->currentProcedure = $this->procedureCollection[++$nextId];
        $this->currentProcedureId[++$nextId];
    }

    protected function proceduresAreInitialized()
    {
        $alreadyInit = !is_null($this->procedureCollection->first());
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

    protected function getCurrentProcedure()
    {
        return $this->procedureCollection[$this->currentProcedureId];
    }

    abstract protected function compositeProcedureIsFinished(Collection $collection, array $arrayOfComposite);

    protected function isCompositeProcedure(string $name)
    {
        if (in_array($name, $this->compositeProcedures)) return true;
        return false;
    }

}


