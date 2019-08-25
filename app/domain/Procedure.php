<?php


namespace App\domain;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity @Table(name="`Procedure`") @HasLifecycleCallbacks
 */
class Procedure extends AbstractProcedure
{
    use DoctrineProcedureLifeCycleCallbacks;
    /**
     * @OneToMany(targetEntity="PartialProcedure", mappedBy="owner", cascade="persist", fetch="EAGER")
     */
    protected $innerProcs;

    /**
     * @ManyToOne(targetEntity="Product")
     **/
    protected $owner;

    public function __construct(string $name, int $idState, Product $product, ?array $innerProcs = null)
    {
        parent::__construct($name, $idState, $product);
        if ($innerProcs) $this->innerProcs = new ArrayCollection(ProcedureFactory::createPartials($innerProcs, $this));
    }

    public function setEnd(): array
    {
        $this->ensureRightInput((bool)$this->getStart(), 'нет отметки о начале данной процедуры');
        $this->ensureRightInput(!$end = $this->getEnd(), "данное событие уже отмечено в журнале: вынос {$this->format($end)}");
        $is_composite_proc = (bool)$this->innerProcs;
        $this->ensureRightInput(
            !$is_composite_proc || $this->innerProcsIsFinished(),
            'нет отметки о завершении внутренних процедур: ',
            $is_composite_proc ? $this->getInnerProcs() : null
        );
        $this->end = new DateTimeImmutable('now');
        return $this->getInfo();
    }


    public function setStart(?string $partial = null): array
    {
        return $info = is_null($partial) ? parent::setStart() : $this->startInner($partial);
    }

    public function getInfo(): array
    {
        $self_info = parent::getInfo();
        $inner_proc_info = $this->innerProcs ? $this->getInnerProcs()->map(function ($partial) {
            return $partial->getInfo();
        })->toArray() : null;
        $self_info[] = $inner_proc_info;
        return $self_info;
    }

    protected function getInnerProcs(): Collection
    {
        $this->ensureRightInput((bool)$this->innerProcs, "{$this->getName()} не имеет вложенных процедур");
        return $this->innerProcs;
    }


    protected function innerProcsIsFinished(): bool
    {
        $inners = $this->getInnerProcs();
        return $inners->forAll(function ($key) use ($inners) {
            return $inners[$key]->isFinished();
        });
    }

    protected function startInner(string $partial_name): array
    {
        foreach ($this->getInnerProcs() as $partial) {
            if ($partial->getName() === $partial_name) {
                $found = $partial;
                break;
            }
        }
        $this->ensureRightInput(
            (bool)($found ?? false),
            "у процедуры {$this->getName()} вложенной процедуры под именем $partial_name не найдено"
        );
        return $info = $found->setStart();
    }

    protected function ensureRightInput(bool $condition, $msg = '', ?Collection $inners = null): void
    {
        $product = $this->getOwner()->getName();
        $number = $this->getOwner()->getNumber();
        !(bool)$inners || $inners_info = array_reduce($inners->toArray(), function ($buffer, $inner) use ($inners) {
            return $buffer .= implode($inner->getInfo(), ', ');
        }, $buffer = '');
        parent::ensureRightInput($condition, " $msg " . ($inners_info ?? ''));
    }
}