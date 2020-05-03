<?php


namespace App\domain\procedures\decorators;
use App\domain\AbstractProduct;
use App\domain\procedures\traits\IProcedureOwner;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;
/**
 * Class ProductOwnerStrategyDecorator
 * @package App\domain\procedures\ownerStrategy
 * @Entity
 */
class ProductOwnerDecorator extends OwnerDecorator
{
    

    /** @var IProcedureOwner|AbstractProduct $owner */
     /** @ManyToOne(targetEntity="App\domain\AbstractProduct", inversedBy="innerProcedures") */
    public IProcedureOwner $owner;
    /** @ManyToOne(targetEntity="App\domain\AbstractProduct", inversedBy="notFinishedInners") */
    protected ?IProcedureOwner $futureOwner;
    /** @ManyToOne(targetEntity="App\domain\AbstractProduct", inversedBy="finishedInners") */
    protected ?IProcedureOwner $pastOwner = null;



}