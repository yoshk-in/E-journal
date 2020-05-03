<?php


namespace App\domain\procedures\traits;


use App\domain\procedures\CompositeProcedure;;
use App\domain\AbstractProduct;
use App\events\Event;
use App\events\IObservable;
use DateTimeInterface;

trait TProcedureProperties
{
    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->ownerStrategy->getName();
    }

    public function getStateName(): string
    {
        return self::STATE_TO_STRING_MAP[$this->state];
    }

    /**
     * @return IProcedureOwner|CompositeProcedure|AbstractProduct
     */
    public function getOwner(): IProcedureOwner
    {
        return $this->ownerStrategy->getOwner();
    }


    public function getOwnerOrder(): int
    {
        return $this->ownerStrategy->getOwnerOrder();
    }

    public function getProductOrder(): int
    {
        return $this->productOrder;
    }

    public function getOrderNumber(): int
    {
        return $this->productOrder;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function getStart(): ?DateTimeInterface
    {
        return $this->start;
    }


    public function getEnd(): ?DateTimeInterface
    {
        return $this->end;
    }

    public function getProduct(): AbstractProduct
    {
        $owner = $this->getOwner();
        while (!$owner instanceof AbstractProduct) {
            $owner = $owner->getOwner();
        }
        return $owner;
    }



    public function beforeEnd(): \DateInterval
    {
        $now = new \DateTime('now');
        return $now->diff($this->end);
    }

    public function isStarted(): bool
    {
        return $this->state > self::READY_TO_START;
    }

    public function isEnded(): bool
    {
        return $this->state === self::ENDED;
    }



}