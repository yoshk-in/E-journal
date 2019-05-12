<?php


namespace App\domain;

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
    protected $minTime;

    public function __construct(string $name, DomainObject $product,int $idStage, $minTime)
    {
        $this->name = $name;
        $this->product = $product;
        $this->idStage = $idStage;
        $this->minTime = new \DateInterval($minTime);
        $this->setStart();
    }

    public function setStart() : void
    {;
        if (is_null($this->start)) $this->start = new \DateTime('now');
        else throw new AppException('данное событие уже отмечено в журнале');
    }

    public function getStart() : \DateTime
    {
        return $this->start;
    }

    /**
     * @return mixed
     */
    public function getEnd() : ?\DateTime
    {
        return $this->end;
    }

    public function setEnd() : void
    {
        if (is_null($this->start)) throw new AppException('в журнале нет отметки' .
        ' о начале данной процедуры - операция не выполнена');
        if (is_null($this->end)) $this->end = new \DateTime('now');
        else throw new AppException('данное событие уже отмечено в журнале');
    }

    /**
     * @return mixed
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getIdStage(): int
    {
        return $this->idStage;
    }

    /**
     * @return \DateInterval
     */
    public function getMinTime(): \DateInterval
    {
        return $this->minTime;
    }

}