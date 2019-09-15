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
    /**
     * @OneToMany(targetEntity="PartialProcedure", mappedBy="owner", cascade="persist")
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

    public function setEnd()
    {
        $this->checkInput((bool)$this->getStart(), $this->errorStr('early_end_casual'));
        $this->checkInput(!$end = $this->getEnd(), $this->errorStr('repeat'));
        !$this->isComposite() ?: $this->checkInput(
            $this->innersAreFinished(),
            $this->errorStr('early_end_composite'),
            array_filter($this->innerProcs->toArray(), function ($inner) {
                if (!$inner->isFinished()) return true;
            })
        );
        $this->end = new DateTimeImmutable('now');
        $this->notifySubscribers();
    }


    public function setStart(?string $partial = null)
    {
        if(is_null($partial)) {
            if ($this->isFinished()) $this->getProduct()->nextProc($this);
            else {
                parent::setStart();
                $this->notifySubscribers();
            }
        } else $this->startInner($partial);
    }

    public function getInfo(): array
    {
        $self_info = parent::getInfo();
        $self_info[] = $this->isComposite() ? array_map(function ($partial) {
            return $partial->getInfo();
        }, $this->innerProcs->toArray()) : null;
        return $self_info;
    }


    protected function innersAreFinished(): bool
    {
        foreach ($this->innerProcs as $inner) {
            if (!$inner->isFinished()) return false;
        }
        return true;
    }

    protected function isComposite(): bool
    {
        return (bool)$this->innerProcs;
    }

    protected function startInner(string $partial_name)
    {
        foreach ($this->innerProcs as $inner) {
            if ($inner->getName() === $partial_name) {
                $found = $inner;
                break;
            }
        }
        $this->checkInput((bool) ($found ?? false),  $this->errorStr('inner_not_fount') . $partial_name);
        $found->setStart();
    }

    protected function checkInput(bool $condition, $msg = '', ?array $inners = []): ?\Exception
    {
        $inners_info_string = '';
        foreach ($inners as $inner) $inners_info_string .= implode(', ',$inner->getInfoToExcept());

        return parent::checkInput($condition, "$msg $inners_info_string  \n");
    }
}