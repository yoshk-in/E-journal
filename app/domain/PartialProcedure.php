<?php


namespace App\domain;

use DateInterval;
use DateTimeImmutable;

/**
 * @Entity
 *
 */
class PartialProcedure extends AbstractProcedure
{
    /** @ManyToOne(targetEntity="CasualProcedure") */
    protected $owner;
    /** @Column(type="string", name="`interval`") */
    protected $interval;



    public function __construct(string $name, int $idState, CasualProcedure $ownerProc, string $interval)
    {
        parent::__construct($name, $idState, $ownerProc);
        $this->interval = $interval;
    }

    public function start(?string  $partial = null)
    {
        parent::_setStart();
        $this->setEnd();
    }

    protected function setEnd() : DateTimeImmutable
    {
        $start = clone $this->start;
        return $this->end = $start->add(new DateInterval($this->interval));
    }

    public function getProduct(): Product
    {
        return $this->getOwner()->getProduct();
    }

    public function isFinished(): bool
    {
        if ($this->state === self::STAGE['end']) return true;
        if ($this->end && (new DateTimeImmutable('now') > $this->end)){
            $this->state = self::STAGE['end'];
            return true;
        };
        return false;
    }

    public function getState() : int
    {
        if ($this->state === self::STAGE['end']) return parent::getState();
        if ($this->end && (new DateTimeImmutable('now') > $this->end)) {
            $this->state = self::STAGE['end'];
            return parent::getState();
        }
        return parent::getState();
    }

}