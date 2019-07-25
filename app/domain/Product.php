<?php

namespace App\domain;

use App\base\exceptions\IncorrectInputException;
use App\base\exceptions\WrongModelException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DateTimeImmutable;
use DateInterval;

/**
 * @MappedSuperClass
 */
abstract class Product
{
    use Notifying;
    /**
     * @Id
     * @Column(type="integer")
     **/
    protected $id;

    protected $procsCollection;

    protected $ttCollection;
    /**
     *
     * @Column(type="integer")
     **/
    protected $currentProcId;

    protected $compositeProcs;

    protected $proceduresRules;


    const THROW_EXCEPT = 0;
    const NOT_THROW_EXCEPT = 1;
    const INIT = 1;

    protected static $procedures;
    protected static $ttProcedureRules;

    public function __construct()
    {
        $this->procsCollection = new ArrayCollection();
        $this->ttCollection = new ArrayCollection();
        $this->ensureProductLogicInit();
    }

    public function initByNumber(int $number): void
    {
        $this->id = $number;
        list($proc, $tt_proc) = $this->getTargetProcNames();
        $this->initProcedures($proc, $tt_proc);
        $this->currentProcId = 0;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function startProcedure() : string
    {
        $this->checkProcsInit(self::INIT);
        $this->checkProcsIsNotFinish();
        $current_process = $this->getCurrentProc();
        $this->checkLastProcEnd();
        $current_process->setStart();
        return $this->notify($current_process);
    }

    public function endProcedure() : string
    {
        $this->checkProcsInit(self::INIT);
        $this->checkProcsIsNotFinish();
        $current_process = $this->getCurrentProc();
        $this->ensureRightInput(
            (bool)($current_process->getStart()),
            ' в журнале нет отметки о начале данной процедуры'
        );
        if ($this->isCompositeProc($current_process)) {
            $this->checkTTisFinish($this->ttCollection, $this->getTTProcedureList());
        }
        $this->checkProcRules($current_process);
        $current_process->setEnd();
        $next_id = $current_process->getStageId();
        $this->currentProcId = ++$next_id;
        return $this->notify($current_process);
    }

    public static function getProcedureList(?string $option = null)
    {
        if (is_null($option)) return array_keys(static::$procedures);
        foreach (static::$procedures as $key => $value) {
            switch ($option) {
                case 'ru' : $ru_result[$key] = $value[0]; break;
                case 'next_state' : $ru_result[$key] = $value[1];
            }
        }
        return $ru_result;

    }

    public static function getTTProcedureList(?string $ru = null)
    {
        foreach (static::$ttProcedureRules as $key => $value) {
            $rule_list[$key] = ($ru === 'ru') ? $value[1] : $value[0];
        }
        return $rule_list;
    }

    public function getProcCollection(): Collection
    {
        $this->checkProcsInit(self::INIT);
        return $this->procsCollection;
    }

    public function getCurrentProc(): ?Procedure
    {
        return $this->procsCollection[$this->currentProcId];
    }

    public function getCurrentProcId(): int
    {
        return $this->currentProcId;
    }

    protected function ensureRightLogic($conditions, string $msg = null)
    {
        if (!$conditions) throw new WrongModelException('have mistake in domain logic program: ' . $msg);
    }

    protected function ensureRightInput($condition, string $msg = null)
    {
        if (!$condition) throw new IncorrectInputException('ошибка: операция не выполнена: ' . $msg);

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

    protected function initProcedures(string $abstr_proc, string $abstr_tt_proc): void
    {
        foreach ($this->getProcedureList() as $id_stage => $procedure) {
            $this->procsCollection->add(new $abstr_proc);
            $this->procsCollection[$id_stage]
                ->setIdentityData($procedure, $this, $id_stage);
        }
        $index = 0;
        foreach ($this->getTTProcedureList() as $tt_procedure => $procedure_time) {
            $this->ttCollection->add(new $abstr_tt_proc);
            $this->ttCollection[$index]
                ->setIdentityData($tt_procedure, $this, $index);
            ++$index;
        }
    }

    public function isCompositeProc(G9Procedure $procedure): bool
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
            $this->ensureRightInput($last_procedure->isFinished(),'окончание прошлой процедуры еше не отмечено');
        }
    }

    protected function checkProcRules(G9Procedure $current_procedure): void
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

    protected function checkProcsIsNotFinish(?int $throw_exp = 0)
    {
        $end_of_last_proc = $this->procsCollection->last()->getEnd();
        $throw_exp ?: $this->ensureRightInput(is_null($end_of_last_proc), ' блок уже сдан на склад');
        if (is_null($end_of_last_proc)) return true;
        return false;
    }

    protected function ensureProductLogicInit()
    {
        $err_msg = 'some properties is not defined in child class! ';
        $this->ensureRightLogic(
            !is_null($this->getProcedureList()) &&
            !is_null($this->getTTProcedureList()) &&
            !is_null($this->proceduresRules) &&
            !is_null($this->compositeProcs) && is_array($this->compositeProcs),
            $err_msg
        );
    }

    abstract protected function checkTTisFinish(Collection $collection, array $arrayOfComposite): void;

    abstract protected function getTargetProcNames(): array;

    abstract public static function getClassTableData();
}


