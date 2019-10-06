<?php


namespace App\domain;


use App\events\Event;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * @Entity
 */

class CompositeProcedure extends Procedure
{
    /**
     * @OneToMany(targetEntity="PartialProcedure", mappedBy="owner", cascade="persist")
     */
    protected $inners;


    public function __construct(string $name, int $idState, Product $product, array $inners)
    {
        parent::__construct($name, $idState, $product);
        $this->inners = new ArrayCollection(ProcedureFactory::createPartials($inners, $this));
    }

    public function setEnd()
    {
        $this->checkInput((bool) $this->innersFinished(), 'внутренние процедуры данного события не завершены:' );
        parent::setEnd();
    }

    public function setStart(?string $partial = null)
    {
        if ($partial) {
            $this->startInner($partial);
            return;
        }
        parent::setStart();
    }

    public function getInners(): ?\ArrayAccess
    {
        return $this->inners;
    }

    public function getCompletedProcedures(): Collection
    {
        return $this->inners->filter(function ($el) {
            return $el->isFinished();
        });
    }

    public function getUncompletedProcedures(): Collection
    {
        return $this->inners->filter(function ($el) {
            return !$el->isFinished();
        });
    }


    protected function innersFinished()
    {
        foreach ($this->inners as $inner) {
            if (!$inner->isFinished()) return false;
        }
        return true;
    }

    protected function startInner(string $partial_name)
    {
        foreach ($this->inners as $inner) {
            if ($inner->getName() === $partial_name) {
                $found = $inner;
                break;
            }
        }
        $this->checkInput((bool)($found ?? false), ' внутренней процедуры с таким именем не найдено: ' . $partial_name);
        $found->setStart();
    }
}