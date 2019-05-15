<?php

namespace App\domaini;

use App\base\exceptions\AppException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
    protected static $procedures;
    protected static $ttProcedureRules;
    protected static $proceduresRules;
    protected $currentProcId;

    public function __construct()
    {
        $this->procsCollection = new ArrayCollection();
        $this->ttCollection = new ArrayCollection();
        $msg = 'some constants is not defined in child class: ';
        $this->ensureRightLogic(
            !is_null(static::$procedures),
            $msg . 'PROCEDURES are not defined'
        );
        $this->ensureRightLogic(
            !is_null(static::$ttProcedureRules),
            $msg . 'TECH_PROC_REG are not defined'
        );
        $this->ensureRightLogic(
            !is_null(static::$proceduresRules),
            $msg . 'PROC_REG are not defined'
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
        //procedures are initialized?
        $this->procsAreInit();
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
        $current_process = $this->getCurrentProc();
        $this->checkLastProcEnd();
        $this->ensureRightLogic(
            is_null($current_process->getStartProc()),
            ' - начало данной процедуры уже отмечено'
        );
        $current_process->setStartProc();
    }

    public function endProcedure()
    {
        $current_process = $this->getCurrentProc();
        $this->checkProcProps($current_process);
        if ($this->isCompositeProc($current_process->getName())) {
            $this->checkTTisFinish($this->ttCollection, static::$ttProcedureRules);
        }
        $current_process->setEndProcedure();
        $next_id = $current_process->getIdStage();
        $this->currentProcId[++$next_id];
    }

    protected function ensureRightLogic($conditions, string $msg = null)
    {
        if (is_array($conditions)) {
            foreach ($conditions as $condition) {
                if (!$condition) {
                    throw new AppException('ошибка: операция не выполнена ' . $msg);
                }
            }
        } else {
            if (!$conditions) {
                throw new AppException('ошибка: операция не выполнена ' . $msg);
            }
        }
    }

    protected function procsAreInit()
    {
        $already_init = $this->procsCollection->first();
        $this->ensureRightLogic($already_init, 'procedures already are initialized');

    }

    protected function initProcedures()
    {
        foreach (static::$procedures as $key => $procedure) {
            $this->procsCollection->add(new Procedure());
            $this->procsCollection[$key]->setIdentityData($procedure, $this, $key);
        }
        $index = 0;
        foreach (static::$ttProcedureRules as $tt_procedure => $procedure_time) {
            $this->ttCollection->add(new TechProcedure());
            $this->ttCollection[$index]->setIdentityData($tt_procedure, $this, $key);
            ++$index;
        }
    }

    protected function getCurrentProc() : Procedure
    {
        return $this->procsCollection[$this->currentProcId];
    }

    protected function isCompositeProc(string $name)
    {
        if (in_array($name, $this->compositeProcs)) {
            return true;
        }
        return false;
    }

    protected function checkProcProps(Procedure $procedure) : void
    {
        $this->ensureRightLogic(
            !is_null($procedure->getStartProc()),
            ' - начало данной процедуры не отмечено'
        );
        $this->ensureRightLogic(
            is_null($procedure->getEndProcedure()),
            ' - окончание данной процедуры уже отмечено'
        );
    }

    protected function checkLastProcEnd() : void
    {
        if ($this->currentProcId !== 0) {
            $last_procedure = $this->procsCollection[--$this->currentProcId];
            $this->ensureRightLogic(
                !is_null($last_procedure->getEnd()),
                'окончание прошлой процедуры еше не отмечено'
            );
        }
    }


    abstract protected function checkTTisFinish(
        Collection $collection, array $arrayOfComposite
    ) : void;


}


