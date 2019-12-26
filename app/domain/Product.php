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

    /**     @Column(type="integer", nullable = true)     */
    protected $number;

    /**     @Column(type="integer", nullable = true)     */
    protected $advancedNumber;

    /**     @Column(type="string")                      */
    protected $name;

    /**     @OneToOne(targetEntity="CasualProcedure")    */
    protected $currentProc;

    /**     @Column(type="boolean")                      */
    protected $finished = false;

    /**     @Column(type="boolean")                       */
    protected $started = false;

    protected $isEndLastProd = false;

    protected static $numberStrategy;

    protected static $changeState = Event::PRODUCT_CHANGE_STATE;
    protected static $procChangeState = AppMsg::PRODUCT_MOVE;
    protected static $productStarted = AppMsg::PRODUCT_STARTED;



    public function __construct(int $number, string $name, ProcedureFactory $factory)
    {
        $this->name = $name;
        $this->procCollection = new ArrayCollection($factory->createProcedures($this));
        self::$numberStrategy->setProductNumber($this, $number,  $this->number);
        $this->notify(AppMsg::PERSIST_NEW);
    }

    public static function setNumberStrategy(NumberStrategy $strategy)
    {
        self::$numberStrategy = $strategy;
    }

    public function isDoubleNumber():bool
    {
        return (bool)self::$numberStrategy instanceof DoubleNumberStrategy;
    }

    public function nextNumber(): ?int
    {
        return self::$numberStrategy->nextNumber($this);
    }

    public function getAdvancedNumber()
    {
        return self::$numberStrategy->getAdvancedNumber($this);
    }


    public function getNameAndNumber(): array
    {
        return [$this->name, $this->number];
    }

    public function setNumbers(?int $number, int $advancedNumber)
    {
        $this->number = $number;
        $this->advancedNumber = $advancedNumber;
        $this->notify(self::$changeState);
    }

    public function getName(): string
    {
        return $this->name;
    }


    public function getNumbersToStrategy(): array
    {
        return [$this->number, $this->advancedNumber];
    }

    public function getNumber(): ?int
    {
        return self::$numberStrategy->getNumber($this);
    }

    public function forward()
    {
        $current_proc = $this->getCurrentProc();
        $state = $current_proc->getState();

        if ($state === AbstractProcedure::STAGE['not_start'] || $state === AbstractProcedure::STAGE['end']) {
            $this->startProcedure();
        } else {

            if (($current_proc instanceof CompositeProcedure) && ($state === AbstractProcedure::STAGE['start'] && !$current_proc->innersFinished())) {

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
       if ($this->isFirstProc()) $this->productStarted() && $this->notify(self::$changeState);
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
        $this->notify(self::$procChangeState);
    }


    public function endProcedure()
    {
        $this->move(function () {
            $this->getCurrentProc()->end();
        });
    }

    public function procEnd(CasualProcedure $procedure)
    {
        if ($this->isLastProc()) ($this->finished = true) && $this->notify(self::$changeState);
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

    public function getActiveProc(): ?AbstractProcedure
    {
        $unfinished = $this->getFirstUnfinishedProc();
        if ($unfinished instanceof CompositeProcedure && $unfinished->isStarted()) return $unfinished->getFirstUnfinishedProc() ?? $unfinished;
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

    public function getFullId()
    {
        $number = $this->getNumber();
        if (is_null($number)) throw new \Exception(' product has not full number');
        return $this->getName() . $number;
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

    protected function productStarted()
    {
        $this->started = true;
        $this->notify($this->name . self::$productStarted);
    }



}


