<?php


namespace App\domain\procedures\proxy;


use App\domain\procedures\interfaces\ProcedureInterface;
use App\domain\procedures\AbstractProcedure;
use App\domain\procedures\decorators\OwnerDecorator;
use App\domain\procedures\traits\TProcedureProxy;
use Doctrine\ORM\Mapping\OneToOne;


abstract class ProcedureProxy extends AbstractProcedure implements ProcedureInterface
{
    use TProcedureProxy;

    /**
     * @OneToOne(targetEntity="App\domain\procedures\CasualProcedure")
     */
    protected ProcedureInterface $subject;

    public function __construct(ProcedureInterface $procedure)
    {
        $this->ownerStrategy = $procedure->getOwnerStrategy();
        $this->subject = $procedure;
    }

    public function __call($name, $arguments)
    {
        $this->subject->$name(...$arguments);
    }
}