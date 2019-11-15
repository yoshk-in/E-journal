<?php

namespace App\domain;

use App\base\AppMsg;
use App\base\exceptions\WrongInputException;
use App\events\{Event, IObservable, TObservable};
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * @Entity
 *
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
     * @OneToMany(targetEntity="CasualProcedure", mappedBy="owner", fetch="EAGER")
     * @OrderBy({"idState" = "ASC"})
     */
    protected $procCollection;

    /**     @Column(type="integer")                     */
    protected $number;

    /**     @Column(type="string")                      */
    protected $name;

    /**     @OneToOne(targetEntity="CasualProcedure")    */
    protected $currentProc;

    /**     @Column(type="boolean")                      */
    protected $finished = false;

    /** @Column(type="boolean")                          */
    protected $started = false;

    protected $isEndLastProd = false;

    protected static $changeState = Event::PRODUCT_MOVE;



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

    public function forward()
    {
        $current_proc = $this->getCurrentProc();
        $state = $current_proc->getState();

        if ($state === AbstractProcedure::STAGE['not_start'] || $state === AbstractProcedure::STAGE['end']) {
            $this->startProcedure();
        } else {

            if (($current_proc instanceof CompositeProcedure) && ($state === AbstractProcedure::STAGE['start'] && !$current_proc->areInnersFinished())) {

                $this->startProcedure($current_proc->getUncompletedProcedures()->first()->getName());
            } else {
                $this->endProcedure();
            }
        }
    }


    public function startProcedure(?string $partial = null)
    {
        $this->move(function ($partial) {
            $this->getCurrentProc()->start($partial);
        }, $partial);

    }

    public function procStart(CasualProcedure $proc)
    {
       if ($this->isFirstProc()) $this->started = true;
    }

    public function nextProc(CasualProcedure $proc)
    {
        $key = $this->procCollection->indexOf($proc);
        $this->currentProc = $this->procCollection[$key + 1];
        $this->notify(self::$changeState);
        $this->startProcedure();
    }

    public function getCompletedProcedures(): Collection
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

    protected function move(\Closure $move, ?string $partial = null)
    {
        $this->isNotFinishedCheck();
        $move($partial);
    }


    public function endProcedure()
    {
        $this->move(function () {
            $this->getCurrentProc()->end();
            $this->notify(self::$changeState);
        });
    }

    public function procEnd(CasualProcedure $procedure)
    {
        if ($this->isLastProc()) $this->finished = true;
    }

    public function getCurrentProc(): CasualProcedure
    {
        return $this->currentProc = $this->currentProc ?? $this->procCollection->first();
    }

    // get current procedure or next if current is finished

    public function getFirstUnfinishedProc(): ?AbstractProcedure
    {
        $current_proc = $this->getCurrentProc();
        $key = $this->procCollection->indexOf($current_proc);
        $res = $current_proc->isFinished() ? $this->procCollection[$key + 1] : $current_proc;
        return $res;
    }

    public function getConcreteUnfinishedProc(): ?AbstractProcedure
    {
        $unfinished = $this->getFirstUnfinishedProc();
        if ($unfinished instanceof CompositeProcedure) return $unfinished->getFirstUnfinishedProc() ?? $unfinished;
        return $unfinished;
    }


    public function getProcedures(): Collection
    {
        return $this->procCollection;
    }

    public function report(string $typeReport)
    {
        $this->notify($typeReport);
    }

    public function isFinished(): bool
    {
        return $this->finished;
    }

    public function isStarted(): bool
    {
        return $this->started;
    }

    public function getId(): int
    {
        return $this->id;
    }

    protected function isNotFinishedCheck()
    {
        if ($this->finished) throw new WrongInputException('ошибка: операция не выполнена: блок уже на складe ' . $this->number);
    }

    protected function isFirstProc(): bool
    {
        if ($this->currentProc === $this->procCollection->first())
        {
            return true;
        }
        return false;
    }

    protected function isLastProc(): bool
    {
        if ($this->currentProc === $this->procCollection->last())
        {
            return $this->isEndLastProd = true;
        }
        return false;
    }



}


