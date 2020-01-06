<?php


namespace App\domain\traits;



use App\domain\CompositeProcedure;

trait TIntervalProcedure
{
    protected \DateTimeImmutable $start;
    /** @Column(type="string", name="`interval`") */
    protected string $interval;
    /**
     * @ManyToOne(targetEntity="CompositeProcedure")
     * @var IIntervalProcedureOwner|IProcedureOwner $owner;
     */
    protected IProcedureOwner $owner;

    /** @OneToOne(targetEntity="CompositeProcedure", nullable=true) */
    protected ?IProcedureOwner $processingOwner = null;

    /** @ManyToOne(targetEntity="CompositeProcedure", nullable=true) */
    protected ?IProcedureOwner $beenProcessedOwner = null;

    /** @ManyToOne(targetEntity="CasualProcedure") */
    protected ?IProcedureOwner $willProcessingOwner = null;

    public function __construct(string $interval)
    {
        $this->interval = $interval;
    }

    protected function concreteProcStart(?string $partial = null)
    {
        $this->willProcessingOwner = null;
        $this->processingOwner = $this->owner;
        $this->setEnd();
    }


    protected function setEnd(): \DateTimeImmutable
    {
        $start = clone $this->start;
        return $this->end = $start->add(new \DateInterval($this->interval));
    }


    public function isEnded(): bool
    {
        if ($this->state === self::NOT_STARTED) return false;
        if ($this->state === self::ENDED) return true;
        if (new \DateTimeImmutable('now') > $this->end) {
            $this->beenProcessedOwner = $this->owner;
            $this->processingOwner = null;
            $this->owner->processInnerEnd($this);
            return true;
        };
        return false;
    }

    public function getState(): int
    {
        //check whether this procedure ended over and if it's true change her state
        $this->isEnded();
        return $this->state;
    }

    public function beforeEnd(): \DateInterval
    {
        $now = new \DateTime('now');
        return $now->diff($this->end);
    }
}