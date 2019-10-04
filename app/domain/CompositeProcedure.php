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
        $this->checkInput((bool) $this->innersNotFinished(), 'внутренние процедуры данного события не завершены:' );
        $this->checkInput((bool)$this->getStart(), ' событие еще не начато');
        $this->checkInput(!$end = $this->getEnd(), 'coбытие уже отмечено');
        $this->end = new DateTimeImmutable('now');
        $this->notify(Event::COMPOSITE_END);
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
        $this->notify(Event::COMPOSITE_START);
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


    protected function innersNotFinished()
    {
        foreach ($this->inners as $inner) {
            if (!$inner->isFinished()) return true;
        }
        return false;
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