<?php


namespace App\domaini;

use App\base\exceptions\AppException;


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
    ): void {
        $this->name = $name;
        $this->product = $product;
        $this->idStage = $idState;
    }


    public function setStartProc(): void
    {
        if (is_null($this->startProcedure)) {
            $this->startProcedure = new \DateTime('now');
        } else {
            throw new AppException('данное событие уже отмечено в журнале');
        }
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
        if (is_null($this->startProcedure)) {
            throw new AppException(
                'в журнале нет отметки' .
                ' о начале данной процедуры - операция не выполнена'
        );
        }
        if (is_null($this->endProcedure)) {
            $now_time = new \DateTime('now');
            if ($now_time < (clone $this->startProcedure)->add($this->interval)) {
                throw new AppException(' minTime exception');
            }
            $this->endProcedure = $now_time;
        } else {
            throw new AppException('данное событие уже отмечено в журнале');
        }
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


}