<?php


namespace App\domain;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * @Entity
 *
 */

class CompositeProcedure extends CasualProcedure
{
    /**
     * @OneToMany(targetEntity="PartialProcedure", mappedBy="owner", fetch="EAGER")
     * @OrderBy({"idState"="ASC"})
     */
    protected $inners;



    public function __construct(string $name, int $idState, Product $product, string $nameAfterEnd, array $inners, ProcedureFactory $factory)
    {
        parent::__construct($name, $idState, $product, $nameAfterEnd);
        $this->inners = new ArrayCollection($factory->createPartials($inners, $this));
    }

    public function end()
    {
        $this->checkInput((bool) $this->innersFinished(), 'внутренние процедуры данного события не завершены:' );
        parent::end();
    }

    public function start(?string $partial = null)
    {
        if ($partial) {
            $this->startInner($partial);
            return;
        }
        parent::start();
    }

    public function getInners(): ?\ArrayAccess
    {
        return $this->inners;
    }

    public function getInnerByName(string $name): PartialProcedure
    {
        foreach ($this->inners as $inner)
        {
            if ($inner->getName() === $name) return $inner;
        }
        throw new \Exception("{$this->getName()} не имеет процедуры с именем $name");
    }

    public function getInnersCount(): int
    {
        return $this->getInners()->count();
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

    public function getFirstUnfinishedProc(): ?PartialProcedure
    {
        if ($this->isFinished()) return null;
        foreach ($this->inners as $inner) {
            if ($inner->isFinished()) continue;
            return $inner;
        }
    }


    public function innersFinished()
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
        $found->start();
    }

}