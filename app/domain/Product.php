<?php

namespace App\domain;

use App\base\AppMsg;
use App\base\exceptions\WrongInputException;
use App\domain\traits\IProcedureOwner;
use App\domain\traits\TManualEndingProcedure;
use App\events\{Event, IObservable, TObservable};
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * @Entity
 *
 */
class Product implements IObservable, IProcedureOwner
{
    use TObservable;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected int $id;

    /**
     * @OneToMany(targetEntity="CasualProcedure", mappedBy="owner", fetch="EAGER")
     * @OrderBy({"idState" = "ASC"})
     */
    protected Collection $procedures;

    /**     @Column(type="integer") */
    protected int $number;

    /**     @Column(type="integer") */
    protected int $advancedNumber;

    /**     @Column(type="string") */
    protected string $name;

    /**     @OneToOne(targetEntity="CasualProcedure") */
    protected AbstractProcedure $currentProc;

    /**     @Column(type="boolean") */
    protected bool $ended = false;

    /**     @Column(type="boolean") */
    protected bool $started = false;

    /**
     * @var string|NumberStrategy $numberStrategy
     */
    protected static string $numberStrategy;
    const PRODUCT_CHANGE_STATE = Event::PRODUCT_CHANGE_STATE;
    const PRODUCT_STARTED = Event::PRODUCT_STARTED;
    const PERSIST = Event::PERSIST_NEW;
    const PRODUCT_CHANGED_NUMBER = 'product_change_number';

    const PRODUCT_FINISHED_ERR = 'ошибка: операция не выполнена: блок уже на складе ';

    public function __construct(int $number, string $name, ProcedureFactory $factory)
    {
        $this->name = $name;
        $this->procedures = new ArrayCollection($factory->createProcedures($this));
        self::$numberStrategy::setProductNumber($this, $number, $this->number);
        $this->currentProc = $this->procedures->first();
        $this->notify(self::PERSIST);
    }

    public static function setNumberStrategy(string $strategy)
    {
        assert(is_a($strategy, $expected = NumberStrategy::class), "expected $expected, $strategy given");
        self::$numberStrategy = $strategy;
    }

    public function isDoubleNumber(): bool
    {
        return (bool)self::$numberStrategy::isDoubleNumber();
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
        assert(is_a($caller = get_called_class(), $expected = NumberStrategy::class), "expected $expected, $caller given");
        $this->number = $number;
        $this->advancedNumber = $advancedNumber;
        $this->notify(self::PRODUCT_CHANGED_NUMBER);
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
        $current_proc->getState() !== $current_proc::STARTED ?
            $this->startProcedure($current_proc->getFirstUnfinishedProcName())
            :
            $this->endProcedure();
    }


    public function startProcedure(?string $partial = null)
    {
        $started_proc = $this->move(fn () => $this->getCurrentProc()->start($partial));
        $this->currentProc = $started_proc;
        if ($started_proc === $this->procedures->first()) {
            $this->started = true;
            $this->notify($this->getStartedEvent());
        }
        $this->notify(self::PRODUCT_CHANGE_STATE);
        $started_proc->notify($started_proc::PROC_CHANGE_STATE);

    }

    public function nextProcStart(AbstractProcedure $proc)
    {
        $this->procedures[$this->currentProc + 1]->start();
    }


    protected function move(\Closure $move): AbstractProcedure
    {
        if ($this->ended) throw new WrongInputException(self::PRODUCT_FINISHED_ERR . $this->number);
        return $move();
    }


    public function endProcedure()
    {
        $ended_proc = $this->move(fn () =>  $this->getCurrentProc()->end());
        if ($ended_proc === $this->procedures->last()) {
            $this->ended = true;
            $this->notify(self::PRODUCT_CHANGE_STATE);
        }
        $ended_proc->notify($ended_proc::PROC_CHANGE_STATE);
    }

    public function getEndedProcedures(): array
    {
        return $this->procedures->slice(0, $this->getProcessingProcedureKey());
    }

    public function getNotEndedProcedures(): array
    {
        return $this->procedures->slice($this->getProcessingProcedureKey());
    }

    public function getCurrentProc(): CasualProcedure
    {
        return $this->currentProc;
    }

    public function getCurrentProcessedProc(?string $partial = null)
    {
        $proc = $this->getCurrentProc();
        return $proc->isComposite() ? $proc->getInnerByName($partial) : $proc;
    }

    // get current procedure or next if current is finished

    public function getFirstNotEndedProc(): ?AbstractProcedure
    {
        return $this->procedures[$this->getProcessingProcedureKey()];
    }

    protected function getProcessingProcedureKey()
    {
        $current_proc = $this->getCurrentProc();
        $current_key = $this->procedures->indexOf($current_proc);
        return $current_proc->isEnded() ? $current_key + 1 : $current_key;
    }

    public function getProcessingProc(): ?AbstractProcedure
    {
        $processing = $this->getFirstNotEndedProc();
        if ($processing->getProcedures() && $processing->isStarted()) return $processing->getProcessingOrNextProc() ?? $processing;
        return $processing;
    }


    public function getProcedures(): Collection
    {
        return $this->procedures;
    }

    public function report(string $typeReport)
    {
        $this->notify($typeReport);
    }

    public function isEnded(): bool
    {
        return $this->ended;
    }

    public function isStarted(): bool
    {
        return $this->started;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStartedEvent(): string
    {
        return $this->name . self::PRODUCT_STARTED;
    }

    public function getEndedEvent(): string
    {
        //@TODO
    }


    function getInnerByName(string $name): AbstractProcedure
    {
        // TODO: Implement getInnerByName() method.
    }
}


