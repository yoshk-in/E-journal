<?php

namespace App\domain;

use App\base\AppException;
use Doctrine\Common\Collections\ArrayCollection;

/** @Entity @Table(name="g9s") * */
class G9 extends DomainObject
{
    const PROCEDURES = [
        'nastroy',
        'technicalTraining',
        'mechanikaOTK',
        'electrikaOTK',
        'mechanikaPZ',
        'electrikaPZ'
    ];
    const TECHNICAL_PROCEDURE_REGULATIONS = [
        'vibro' => 'PT30M',
        'progon' => 'PT2H',
        'moroz' => 'PT2H',
        'rest' => 'PT2H',
        'jara' => 'PT2H'
    ];

    protected $procedureCollection;
    protected $TTCollection;
    protected $currentProcedure;
    protected $compositeProcedure;

    public function __construct($number)
    {
        parent::__construct($number);
        $this->procedureCollection = new ArrayCollection();
        $this->TTCollection = new ArrayCollection();
//        $this->procedureList = $this->doProcedureList('technicalTraining');
//        $this->maxSizeProcedures = count($this->procedureList);
        $this->compositeProcedure = 'technicalTraining';
        $this->ensure(
            array_search($this->compositeProcedure, self::PROCEDURES) !== false,
            "{$this->compositeProcedure} must be equals 'technicalTraining'"
            );
        $this->setCurrentProcedure();
    }


    public function nextProcedure()
    {
        if (!is_null($this->currentProcedure)) {
            $this->ensure(
                $this->currentProcedure->getEnd() !== null,
                ' - новое испытвние не может быть начато, т.к. отсутствует отметка об окончания предыдущего');
        }
        $this->currentProcedure = $this->getNewProcedure();
        $this->procedureCollection->add($this->currentProcedure);
    }

    public function endProcedure()
    {
        if ($this->currentProcedure->getName() === $this->compositeProcedure) {
            $this->ensure(
                $this->TechTrainingIsFinished(), ' - техтренировка по времени еще не закончена'
                . ',т.к. в журнале отстутвуют полностью или частично данные о входящих в нее испытаний'
            );
        }
        $this->getCurrentProcedure()->end();
    }

    public function nextTraining($nameProcedure)
    {
        $this->ensure($this->currentProcedure->getName() === $this->compositeProcedure, ' - блок на ' .
            'техтренировку еще не поступал');

        $idStage = $this->getTechProcedureId($nameProcedure);

        $this->TTCollection->exists(function ($entity) use ($nameProcedure) {
            $this->ensure($entity->getName() !== $nameProcedure, ' - данное испытание ' .
                'уже проведено');
        });

        $time = self::TECHNICAL_PROCEDURE_REGULATIONS[$nameProcedure];        
        $this->ensureProcedureRegulations();
        $techProcedure = new TechnicalProcedure($nameProcedure, $this, $idStage, $time);
        $this->TTCollection->add($techProcedure);
    }

    protected function getCurrentProcedure()
    {
        return $this->currentProcedure;
    }

    protected function ensureProcedureRegulations()
    {

        if ($this->TTCollection->last()) {
            $current = $this->TTCollection->last();
            if ($current->getEnd() < (new \DateTime('now'))) return;
            throw new AppException(' предыдущая операция еще не завершена по времени');
        }

    }

    protected function getTechProcedureId($nameProcedure)
    {
        $TTArray = array_keys(self::TECHNICAL_PROCEDURE_REGULATIONS);
        $idStage = array_search($nameProcedure, $TTArray);
        $this->ensure($idStage !== false, ' - неправильное имя испытания');
        return $idStage;

    }

    protected function getNewProcedure(): Procedure
    {
        if (!is_null($this->currentProcedure))  {
            $currentName = $this->currentProcedure->getName();
            $number = array_search($currentName,self::PROCEDURES);
            $nextName = self::PROCEDURES[++$number];
            $this->ensure(
                array_search($nextName, self::PROCEDURES) !== false,
                ' блок уже на складе (также возможна ошибка в логике формирования новых процедур)'
                );
        } else {
            $number = 0;
            $nextName = self::PROCEDURES[$number];

        }
        return new Procedure($nextName, $this, $number);
    }

    protected function setCurrentProcedure()
    {
        if ($this->procedureCollection->isEmpty()) return;
        $this->currentProcedure = $this->procedureCollection->last();
    }

    protected function TechTrainingIsFinished()
    {
        $isFinished = (bool)(($this->TTCollection->count() === self::TECHNICAL_PROCEDURE_REGULATIONS)
        && $this->TTCollection->last()->isFinised());
        return $isFinished;
    }

}

