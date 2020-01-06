<?php


namespace App\domain;


use App\base\AppMsg;
use App\domain\exception\TProcCheckInput;
use App\domain\traits\IProcedureOwner;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\events\{Event, IObservable, TObservable};


abstract class AbstractProcedure implements IObservable
{
    use TObservable, TProcCheckInput;

    /** @Column(type="datetime_immutable", nullable=true) */
    protected \DateTimeImmutable $start;

    /** @Column(type="datetime_immutable", nullable=true) */

    protected \DateTimeImmutable $end;

    /** @Column(type="string") */
    protected string $name;

    /** @Column(type="integer") */
    protected int $procedureOrderNumber;

    /** @Column(type="integer") */
    protected int $state = self::NOT_STARTED;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected int $id;

    /** @var IProcedureOwner|CompositeProcedure|Product */
    protected IProcedureOwner $owner;

    const NOT_STARTED = 0;
    const STARTED = 1;
    const ENDED = 2;


    const PROC_CHANGE_STATE = Event::PROCEDURE_CHANGE_STATE;

    public function __construct(string $name, int $idState, IProcedureOwner $owner)
    {
        $this->name = $name;
        $this->procedureOrderNumber = $idState;
        $this->owner = $owner;
        $this->notify(Event::PERSIST_NEW);
    }

    public function start(?string $partialName = null): self
    {
        if ($this->isEnded()) {
            $this->owner->nextProc($this);
            return $this;
        }
        $this->checkInputCondition(is_null($this->start), 'событие уже отмечено');
        $this->start = new \DateTimeImmutable('now');
        $this->concreteProcStart($partialName);
        return $this;
    }



    public function getOrderNumber(): int
    {
        return $this->procedureOrderNumber;
    }


    public function isEnded(): bool
    {
        return $this->getState() === self::ENDED;
    }


    public function isStarted(): bool
    {
        return $this->getState() === self::STARTED;
    }

    public function isNotStarted(): bool
    {
        return $this->getState() === self::NOT_STARTED;
    }

    public function getName(): string
    {
        return $this->name;
    }


    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function getState(): int
    {
        return $this->state;
    }


    public function getProduct(): Product
    {
        return $this->getOwner();
    }

    /**
     * @return IProcedureOwner|CompositeProcedure|Product
     */
    public function getOwner(): IProcedureOwner
    {
        return $this->owner;
    }

    public function getInnersCount(): int
    {
        return 0;
    }

    public function getProcessingOrNextProc(): ?AbstractProcedure
    {
        return null;
    }

    public function getFirstUnfinishedProcName(): ?string
    {
        return null;
    }

    public function getInnerByName(string $name): ?AbstractProcedure
    {
        return null;
    }

    public function getEndedProcedures(): ?Collection
    {
        return null;
    }

    public function getNotEndedProcedures(): ?Collection
    {
        return null;
    }

    public function innersEnded(): bool
    {
        return true;
    }

    public function getProcedures(): Collection
    {
        return new ArrayCollection();
    }

    public function isComposite(): bool
    {
        return false;
    }



    abstract protected function concreteProcStart(?string $partial = null);





}
