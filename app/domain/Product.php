<?php

namespace App\domain;

use App\base\exceptions\IncorrectInputException;
use App\base\exceptions\WrongModelException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DateTimeImmutable;
use DateInterval;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

abstract class Product
{
    /**
     * @Id
     * @Column(type="integer")
     **/
    protected $number;
    protected $procsCollection;
    protected $ttCollection;
    protected $compositeProcs;
    protected $procedures;
    protected $ttProcedureRules;
    protected $proceduresRules;
    protected $currentProcId;

    public function __construct()
    {
        $this->procsCollection = new ArrayCollection();
        $this->ttCollection = new ArrayCollection();
        $err_msg = 'some constants is not defined in child class: ';
        $this->ensureRightLogic(
            !is_null($this->procedures),
            $err_msg . 'PROCEDURES are not defined'
        );
        $this->ensureRightLogic(
            !is_null($this->ttProcedureRules),
            $err_msg . 'TECH_PROC_REG are not defined'
        );
        $this->ensureRightLogic(
            !is_null($this->proceduresRules),
            $err_msg . 'PROC_REG are not defined'
        );
        $this->ensureRightLogic(
            !is_null($this->compositeProcs),
            'prop compositeProcedure is required'
        );
        $this->ensureRightLogic(
            is_array($this->compositeProcs),
            'compositeProcedure must be array'
        );
    }

    public function initByNumber(int $number): void
    {
        $this->number = $number;
        $this->initProcedures();
        $this->currentProcId = 0;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function startProcedure()
    {
        $this->checkProcsInit(true);
        $this->checkProcsIsNotFinish();
        $current_process = $this->getCurrentProc();
        $this->checkLastProcEnd();
        $current_process->setStart();
    }

    public function endProcedure()
    {
        $this->checkProcsInit(true);
        $this->checkProcsIsNotFinish();
        $current_process = $this->getCurrentProc();
        $this->ensureRightInput(
            !is_null($current_process->getStart()),
            ' в журнале нет отметки о начале данной процедуры'
        );
        if ($this->isCompositeProc($current_process)) {
            $this->checkTTisFinish($this->ttCollection, $this->ttProcedureRules);
        }
        $this->checkProcRules($current_process);
        $current_process->setEnd();
        $next_id = $current_process->getStageId();
        $this->currentProcId = ++$next_id;
    }

    public function getCurrentProc(): ?Procedure
    {
        $this->checkProcsInit(true);
        return $this->procsCollection[$this->currentProcId];
    }

    public function getProcCollection() : Collection
    {
        $this->checkProcsInit(true);
        return $this->procsCollection;
    }

    protected function ensureRightLogic($conditions, string $msg = null)
    {
        if (!$conditions) {
            throw new WrongModelException(
                'have mistake in domain logic program: ' . $msg
            );
        }

    }

    protected function ensureRightInput($condition, string $msg = null)
    {
        if (!$condition) {
            throw new IncorrectInputException(
                'ошибка: операция не выполнена: ' . $msg
            );
        }
    }

    protected function checkProcsInit(bool $inited = true)
    {
        $already_init = $this->procsCollection->first();
        $err_msg = 'procedures are not initialized yet';
        if (!$inited) {
            $already_init = !$already_init;
            $err_msg = 'procedures already are initialized';
        }
        $this->ensureRightLogic($already_init, $err_msg);

    }

    protected function initProcedures() : void
    {
        foreach ($this->procedures as $id_stage => $procedure) {
            $this->procsCollection->add(new Procedure());
            $this->procsCollection[$id_stage]
                ->setIdentityData($procedure, $this, $id_stage);
        }
        $index = 0;
        foreach ($this->ttProcedureRules as $tt_procedure => $procedure_time) {
            $this->ttCollection->add(new TechProcedure());
            $this->ttCollection[$index]
                ->setIdentityData($tt_procedure, $this, $index);
            ++$index;
        }
    }

    protected function isCompositeProc(Procedure $procedure) : bool
    {
        if (in_array($procedure->getName(), $this->compositeProcs)) {
            return true;
        }
        return false;
    }

    protected function checkLastProcEnd(): void
    {
        if ($this->currentProcId !== 0) {
            $current_id = $this->currentProcId;
            $last_procedure = $this->procsCollection[--$current_id];
            $this->ensureRightInput(
                $last_procedure->isFinished(),
                'окончание прошлой процедуры еше не отмечено'
            );
        }
    }

    protected function checkProcRules(Procedure $current_procedure) : void
    {
        $start = $current_procedure->getStart();
        $interval = new DateInterval($this->proceduresRules['minTime']);
        $end_by_rules = $start->add($interval);
        $this->ensureRightInput(
            new DateTimeImmutable('now') >= $end_by_rules,
            '- минимальное время проведения процедуры' .
            $interval->format(' %H часов %i минут %s секунд')
        );
    }

    protected function checkProcsIsNotFinish()
    {
        $end_of_last_proc = $this->procsCollection->last()->getEnd();
        $this->ensureRightInput(is_null($end_of_last_proc), ' блок уже сдан на склад');
    }

    abstract protected function checkTTisFinish(
        Collection $collection, array $arrayOfComposite
    ): void;


}


