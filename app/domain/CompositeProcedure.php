<?php


namespace App\domain;


use App\events\Event;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @Entity
 */

class CompositeProcedure extends Procedure
{
    /**
     * @OneToMany(targetEntity="PartialProcedure", mappedBy="owner", cascade="persist")
     */
    protected $innerProcs;

    public function __construct(string $name, int $idState, Product $product, array $innerProcs)
    {
        parent::__construct($name, $idState, $product);
        $this->innerProcs = new ArrayCollection(ProcedureFactory::createPartials($innerProcs, $this));
    }

    public function setEnd()
    {
        $this->checkInput((bool) $inner = $this->innersNotFinished(), 'внутренние процедуры данного события не завершены: ' . $inner->getName() );
        parent::setEnd();
        $this->notify(Event::END);
    }

    public function setStart(?string $partial = null)
    {
        if ($partial) {
            $this->startInner($partial);
            return;
        }
        if ($this->isFinished()) {
            $this->getProduct()->nextProc($this);
            return;
        }
        parent::setStart();
        $this->notify(Event::START);
    }

    public function getInners(): ?\ArrayAccess
    {
        return $this->innerProcs;
    }


    protected function innersNotFinished()
    {
        foreach ($this->innerProcs as $inner) {
            if (!$inner->isFinished()) return $inner;
        }
        return false;
    }

    protected function startInner(string $partial_name)
    {
        foreach ($this->innerProcs as $inner) {
            if ($inner->getName() === $partial_name) {
                $found = $inner;
                break;
            }
        }
        $this->checkInput((bool)($found ?? false), ' внутренней процедуры с таким именем не найдено: ' . $partial_name);
        $found->setStart();
    }
}