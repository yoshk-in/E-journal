<?php


namespace App\domaini;

use App\base\exceptions\AppException;
use App\base\exceptions\IncorrectInputException;
use App\base\exceptions\WrongModelException;


/**
 * @Entity @Table(name="states")
 *
 **/
class Procedure
{
    protected $startProcedure;
    protected $endProcedure;
    protected $name;
    protected $product;
    protected $idStage;
    protected $interval;
    protected $init;

    public function __construct()
    {
    }

    public function setIdentityData(
        string $name, Product $product, int $idState
    ): void
    {
        $this->name = $name;
        $this->product = $product;
        $this->idStage = $idState;
    }


    public function setStartProc(): void
    {
        $this->ensureRighInput(
            is_null($this->startProcedure),
            'данное событие уже отмечено в журнале'
        );
        $this->startProcedure = new \DateTime('now');
    }

    public function getStartProc(): \DateTime
    {
        return $this->startProcedure;
    }

    public function getEndProcedure(): ?\DateTime
    {
        return $this->endProcedure;
    }

    public function setEndProcedure(): void
    {
        if (is_null($this->interval)) {
            throw new WrongModelException('interval is not set');
        }
        $this->ensureRighInput(
            !is_null($this->startProcedure),
            'в журнале нет отметки' .
            ' о начале данной процедуры '
        );
        $this->ensureRighInput(
            is_null($this->endProcedure), 'данное событие уже отмечено в журнале'
        );
        $now_time = new \DateTime('now');
        $this->ensureRighInput(
            $now_time < (clone $this->startProcedure)->add($this->interval),
            ' не соблюден минимальный интервал выполнения ' .
            'процедуры - по умолчанию:' .
            $this->interval->format('%H часов % минут % секунд')
        );
        $this->endProcedure = $now_time;

    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getIdStage(): int
    {
        return $this->idStage;
    }

    public function isFinished() : bool
    {

        if ($this->endProcedure) {
            return true;
        }
        return false;
    }

    protected function ensureRighInput(bool $condition, $msg = null)
    {
        if (!$condition) {
            throw new IncorrectInputException(
                'ошибка: операция не выполнена ' . $msg
            );
        }

    }

}