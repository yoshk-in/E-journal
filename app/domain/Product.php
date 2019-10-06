<?php

namespace App\domain;

use App\base\exceptions\WrongInputException;
use App\events\{IObservable, TObservable};
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * @Entity
 */
class Product implements IObservable
{
    use TObservable;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @OneToMany(targetEntity="Procedure", mappedBy="owner", cascade="persist")
     * @OrderBy({"idState" = "ASC"})
     */
    protected $procCollection;

    /**     @Column(type="integer")                 */
    protected $number;

    /**     @Column(type="string")                  */
    protected $name;

    /**     @OneToOne(targetEntity="Procedure")     */
    protected $currentProc;

    /**     @Column(type="boolean")                 */
    protected $finished = false;


    public function __construct(int $number, string $name, ProcedureFactory $factory)
    {
        $this->number = $number;
        $this->name = $name;
        $this->procCollection = new ArrayCollection($factory->createProcedures($this));
    }


    public function getNameAndNumber(): array
    {
        return [$this->name, $this->number];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNumber(): int
    {
        return $this->number;
    }


    public function startProcedure(?string $partial = null)
    {
        $this->isNotFinishedCheck();
        if (is_null($this->currentProc)) {
            $this->currentProc = $this->procCollection->first();
        }
        $this->currentProc->setStart($partial);
    }

    public function nextProc(Procedure $proc)
    {
        $this->currentProc = $this->procCollection[$next_id = $proc->getIdState() + 1];
        $this->startProcedure();
    }

    public function getCompleteProcedures(): Collection
    {
        return $this->procCollection->filter(function ($el) {
            return $el->isFinished();
        });
    }

    public function getUncompletedProcedures(): Collection
    {
        return $this->procCollection->filter(function ($el) {
            return !$el->isFinished();
        });
    }


    public function endProcedure()
    {
        $this->isNotFinishedCheck();
        $this->currentProc->setEnd();
        if ($this->currentProc === $this->procCollection->last()) $this->finished = true;
    }

    public function getCurrentProc(): Procedure
    {
        return $this->currentProc;
    }


    public function getProcedures(): Collection
    {
        return $this->procCollection;
    }

    public function report(string $typeReport)
    {
        $this->notify($typeReport);
    }


    protected function isNotFinishedCheck()
    {
        if ($this->finished) throw new WrongInputException('ошибка: операция не выполнена: блок уже на складe' . $this->number);
    }

}


