<?php


namespace App\domain\procedures\traits;


use App\domain\procedures\interfaces\ProcedureInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\OrderBy;

trait TCompositeProcedureOwner
{
    /**
     * @OneToMany(targetEntity="App\domain\procedures\decorators\ProcedureOwnerDecorator", mappedBy="owner",  indexBy="name")
     * @OrderBy({"ownerOrder"="ASC"})
     */
    protected Collection $innerProcedures;

    /**
     * @OneToMany(targetEntity="App\domain\procedures\decorators\ProcedureOwnerDecorator", mappedBy="owner",  indexBy="name")
     * @OrderBy({"ownerOrder"="ASC"})
     */
    protected Collection $finishedInners;

    /**
     * @OneToMany(targetEntity="App\domain\procedures\decorators\ProcedureOwnerDecorator", mappedBy="owner",  indexBy="name")
     * @OrderBy({"ownerOrder"="ASC"})
     */
    protected Collection $notFinishedInners;


    /** @OneToOne(targetEntity="App\domain\procedures\decorators\ProcedureOwnerDecorator") */
    protected ?ProcedureInterface $processingInner = null;
}