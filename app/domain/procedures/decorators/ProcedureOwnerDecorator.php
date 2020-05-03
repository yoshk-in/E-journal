<?php


namespace App\domain\procedures\decorators;


use App\domain\procedures\CompositeProcedure;
use App\domain\procedures\traits\IProcedureOwner;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * @Entity()
 */
class ProcedureOwnerDecorator extends OwnerDecorator
{
    /** @var IProcedureOwner|CompositeProcedure */
    /** @ManyToOne(targetEntity="App\domain\procedures\CompositeProcedure", inversedBy="innerProcedures") */
    public IProcedureOwner $owner;
    /** @ManyToOne(targetEntity="App\domain\procedures\CompositeProcedure", inversedBy="notFinishedInners") */
    protected ?IProcedureOwner $futureOwner;
    /** @ManyToOne(targetEntity="App\domain\procedures\CompositeProcedure", inversedBy="finishedInners") */
    protected ?IProcedureOwner $pastOwner;

}