<?php

namespace App\domain;

use App\base\AppException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class DomainObject
{
    /**
     *  @Id
     *  @Column(type="integer")
     **/
    protected $number;
    protected $procedureCollection;
    protected $TTCollection;
    protected $compositeProcedure;
    protected static $PROCEDURES;
    protected static $TECHNICAL_PROCEDURES_REGULATIONS;
    protected static $PROCEDURES_REGULATIONS;

    public function __construct(int $number)
    {
        $this->number = $number;
        $this->procedureCollection = new ArrayCollection();
        $this->TTCollection = new ArrayCollection();
        $msg = 'some constants is not defined in child class: ';
        $this->ensure( !is_null(static::$PROCEDURES), $msg . 'PROCEDURES are not defined');
        $this->ensure(!is_null(static::$TECHNICAL_PROCEDURES_REGULATIONS), $msg . 'TECH_PROC_REG are not defined');
        $this->ensure( !is_null(static::$PROCEDURES_REGULATIONS), $msg . 'PROC_REG are not defined');
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function ensure(bool $condition, string $msg = null)
    {
        if (!$condition) throw new AppException('ошибка: операция не выполнена ' . $msg);
    }

    public function nextProcedure()
    {
        $last = $this->procedureCollection->last();
        $errMsg = ' - новое испытание не может быть начато,отсутствует отметка об окончания предыдущего';
        if ($last) $this->ensure(!is_null($last->getEnd()), $errMsg);
        $next = $this->getNewProcedure();
        $this->procedureCollection->add($next);
    }

/*    public function newProcedure(Procedure $procedure)
    {
        $last = $this->procedureCollection->last();
        $errMsg = ' - новое испытание не может быть начато,отсутствует отметка об окончания предыдущего';
        if ($last) $this->ensure(!is_null($last->getEnd()), $errMsg);
        $this->
    }*/


    public function endProcedure()
    {
        $procCollection = $this->procedureCollection;
        $last = $procCollection->last();
        $this->ensure($last->getStart() !== false, ' - нет отметки о начале данной процедуры');
        $errMsg = ' - техтренировка по времени еще не закончена,т.к. в журнале отстутвуют полностью или частично данные о входящих в нее испытаний';
        if ($this->isCompositeProc($last))
            $this->ensure($this->proceduresAreFinished($procCollection, static::$PROCEDURES), $errMsg);

        $last->getStart();
        $errMsg = " - минимальное время процедуры " . $last->getMinTime()->format('%h часов %i минут %s секунд') .
            ", время ее начала {$last->getStart()->format('H:i:s')}";
        $endTime =(clone $last->getStart())->add($last->getMinTime());
        $this->ensure(new \DateTime('now')  > $endTime, $errMsg);
        $last->setEnd();
    }

    public function nextTraining($procedureName)
    {
        $isCompositeProcedure = $this->procedureCollection->last()->getName() === $this->compositeProcedure;
        $this->ensure($isCompositeProcedure, ' - блок на техтренировку еще не поступал');
        $idStage = $this->getTechProcedureId($procedureName);
        $isNotARepeat = is_null($this->getProcedureByName($this->TTCollection, $procedureName));
        $this->ensure($isNotARepeat, ' - данное испытание уже проведено');
        $time = static::$TECHNICAL_PROCEDURES_REGULATIONS[$procedureName];
        $this->ensure($this->TTCollection->last()->isFinised(), ' предыдущая операция еще не завершена по времени');
        $techProcedure = new TechnicalProcedure($procedureName, $this, $idStage, $time);
        $this->TTCollection->add($techProcedure);
    }

    public function getProcedureProp(string $prop, ?string $procedureName = null)
    {
        if (is_null($procedureName)) $procedureName = $this->procedureCollection->last()->getName();
        $procedure = $this->findProcedureByName($procedureName);
        $this->ensure(!is_null($procedure), ' - в журнале нет отметки о данной процедуре');
        $procProp = "get" . strtolower($prop);
        new \ReflectionMethod($procedure, $procProp);
        return $procedure->$procProp();
    }

    protected function isCompositeProc(Procedure $proc)
    {
        if ($proc->getName() === $this->compositeProcedure) return true;
        return false;
    }

    protected function findProcedureByName($procedureName)
    {
        $this->isValidProcedureName($procedureName, 1);
        $procedure = $this->getProcedureByName($this->procedureCollection, $procedureName) ?:
            $this->getProcedureByName($this->TTCollection, $procedureName);
        return $procedure;

    }

    protected function isValidProcedureName(string $name, int $searchInTT = 0, $errMsg = 'wrong procedure name')
    {
        $validInProcs = (array_search($name, static::$PROCEDURES) !== false);
        $validInTechProcs = $searchInTT ? true : (array_key_exists($name, static::$TECHNICAL_PROCEDURES_REGULATIONS));
        $this->ensure($validInProcs || $validInTechProcs, $errMsg);
        return true;
    }

    protected function getProcedureByName(Collection $collection, $procedureName)
    {
        foreach ($collection as $key => $procedure) if ($procedure->getName() === $procedureName) return $procedure;
    }


    protected function getTechProcedureId($nameProcedure)
    {
        $TTArray = array_keys(static::$TECHNICAL_PROCEDURES_REGULATIONS);
        $idStage = array_search($nameProcedure, $TTArray);
        $this->ensure($idStage !== false, ' - неправильное имя испытания');
        return $idStage;

    }

    protected function getNewProcedure(): Procedure
    {
        $last = $this->procedureCollection->last();
        if ($last) {
            $nextNumber = array_search($last->getName(), static::$PROCEDURES) + 1;
            $this->isValidProcedureName(static::$PROCEDURES[$nextNumber], 0, ' - блок уже на складе');
        } else {
            $nextNumber = 0;
        }
        $nextName = static::$PROCEDURES[$nextNumber];
        return new Procedure($nextName, $this, $nextNumber, static::$PROCEDURES_REGULATIONS['minProcTime']);
    }


    protected function proceduresAreFinished(Collection $procCollection, array $standardArray)
    {
        $count = $procCollection->count();
        $standardCount = count($standardArray);
        $isFinished = (bool)(($count === $standardCount) && $procCollection->last()->isFinised());
        return $isFinished;
    }



}
