<?php


namespace App\domain;


use App\domain\traits\IBeforeEndProcedure;
use App\domain\traits\IProcedureOwner;
use App\domain\traits\TIntervalProcedure;

/**
 * @Entity
 *
 */
class PartialProcedure extends AbstractProcedure implements IBeforeEndProcedure
{
    use TIntervalProcedure{TIntervalProcedure::__construct as interval__construct;}

    /**
     * @ManyToOne(targetEntity="CompositeProcedure")
     * @var IProcedureOwner|CompositeProcedure $owner;
     */
    protected IProcedureOwner $owner;



    public function __construct(string $name, int $idState, CompositeProcedure $ownerProc, string $interval)
    {
        parent::__construct($name, $idState, $ownerProc);
        $this->interval__construct($interval);
        $this->willProcessingOwner = $ownerProc;
    }




    public function getProduct(): Product
    {
        $owner = $this->owner;
        while (!$owner instanceof Product) {
            $owner = $this->owner->getOwner();
        }
        return $owner;
    }


}