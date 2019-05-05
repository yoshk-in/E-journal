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
    protected $minPeriod;

    public function __construct(string $name, DomainObject $product,int $idStage)
    {
        $this->name = $name;
        $this->product = $product;
        $this->idStage = $idStage;
        $this->setStart();
        $this->minPeriod = new \DateInterval(ProcedureRequirements::MIN_PROCEDURE_TIME);
    }

    public function setStart() : void
    {
        $this->start = new \DateTime('now');
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

    public function end() : void
    {
        if (is_null($this->start)) throw new AppException('в журнале нет отметки' .
        ' о начале данной процедуры - операция не выполнена');
        $endPeriod = (clone (new \DateTime('now')))->add($this->minPeriod);
        $now = new \DateTime('now');
        if ($endPeriod < $now)
        $this->end = $now;
        else throw new AppException(
            'операция не выполнена: минимальное время отведенное на выполнение процедуры  - ' .
            $this->minPeriod->format('%i минут : время сейчас ' . $now->format('H:i:s')));
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

 /*   public function isValidCall($class)
    {
        $targetObject = $this->targetObject();
        if ($class instanceof $targetObject) {
            return;
        }
        throw new AppException('wrong called class');
    }

    public function targetObject()
    {
        return '\App\domain\DomainObject';
    }*/
}