<?php


namespace App\domain;

use DateInterval;
use App\events\{Event};

/**
 * @Entity
 */
class PartialProcedure extends AbstractProcedure
{
    /** @ManyToOne(targetEntity="Procedure") */
    protected $owner;

    /** @Column(type="string", name="`interval`") */
    protected $interval;

    public function __construct(string $name, int $idState, Procedure $ownerProc, string $interval)
    {
        parent::__construct($name, $idState, $ownerProc);
        $this->interval = $interval;
    }

    public function setStart(?string  $partial = null)
    {
        parent::setStart();
        $this->setEnd();
        $this->notify(Event::PARTIAL_START);
    }

    protected function setEnd() : \DateTimeImmutable
    {
        $start = clone $this->start;
        return $this->end = $start->add(new DateInterval($this->interval));
    }

    public function getProduct(): Product
    {
        return $this->getOwner()->getProduct();
    }


}