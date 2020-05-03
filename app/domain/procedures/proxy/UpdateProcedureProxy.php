<?php


namespace App\domain\procedures\proxy;


use App\domain\procedures\interfaces\ProcedureInterface;
use App\events\Event;
use App\events\traits\TObservable;
use App\repository\AfterRequestCallBuffer;
use App\repository\DB;
use App\repository\traits\TDatabase;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity()
 */
class UpdateProcedureProxy extends ProcedureProxy
{
    use TDatabase, TObservable;


    public function __construct(ProcedureInterface $procedure)
    {
        parent::__construct($procedure);
        $this->setProxyEvent();
        $this->persist();
    }

    public function __call($name, $arguments)
    {
        $this->subject->needsToUpdate() ? $this->remove() : $this->setProxyEvent();
        $this->dropProxyForCurrentRequest();
        parent::__call($name, $arguments);
    }

    protected function setProxyEvent()
    {
        $this->event();
    }

    public function setProxy()
    {
        $this->ownerStrategy->setNewSubject($this);
    }

    protected function dropProxyForCurrentRequest()
    {
        $this->ownerStrategy->setNewSubject($this->subject);
    }



}