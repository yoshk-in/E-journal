<?php


namespace App\domaini;

use App\base\AppException;
use App\domain\ProcedureRequirements;

/**
 * @Entity @Table(name="states")
 *
 **/
class Procedure
{
    protected $start;
    protected $end;
    protected $name;
    protected $product;
    protected $idStage;
    protected $interval;
    protected $init;

    public function __construct()
    {

    }

    public function setIdentityData(string $name, DomainObject $product, int $idState): void
    {
        $this->name = $name;
        $this->product = $product;
        $this->idStage = $idState;
    }


    public function setStart(): void
    {
        if (is_null($this->start)) $this->start = new \DateTime('now');
        else throw new AppException('данное событие уже отмечено в журнале');
    }

    public function getStart(): \DateTime
    {
        return $this->start;
    }

    public function getEnd(): ?\DateTime
    {
        return $this->end;
    }

    public function setEnd(): void
    {
        if (is_null($this->start)) throw new AppException('в журнале нет отметки' .
            ' о начале данной процедуры - операция не выполнена');
        if (is_null($this->end)) {
            $now = new \DateTime('now');
            if ($now < (clone $this->start)->add($this->interval))
                throw new AppException(' minTime exception');
            $this->end = $now;
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