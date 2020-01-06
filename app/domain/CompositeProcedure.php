<?php


namespace App\domain;


use App\domain\traits\IIntervalProcedureOwner;
use App\domain\traits\IProcedureOwner;
use App\domain\traits\TIntervalProcedure;
use App\domain\traits\TManualEndingProcedure;
use App\GUI\grid\style\Style;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\base\exceptions\WrongInputException;


/**
 * @Entity
 *
 */
class CompositeProcedure extends CasualProcedure implements IIntervalProcedureOwner
{
    /**
     * @OneToMany(targetEntity="PartialProcedure", mappedBy="owner", fetch="EAGER")
     * @OrderBy({"idState"="ASC"})
     */
    protected Collection $inners;

    const INNERS_NOT_FINISHED = ' внутренние процедуры данного события не завершены:';
    const INNER_NOT_FOUND = ' внутренней процедуры с таким именем не найдено: ';

    /** @Column(type='array') */
    protected array $partialsKey = [];
    /**
     * @OneToMany(targetEntity="PartialProcedure", mappedBy="owner", fetch="EAGER")
     * @OrderBy({"idState"="ASC"}
     */
    protected Collection $notEndedInners;
    /**
     * @OneToOne(targetEntity="PartialProcedure", mappedBy="owner", fetch="EAGER", nullable=true)
     * @OrderBy({"idState"="ASC"}
     */
    protected ?PartialProcedure $processingInner = null;
    /**
     * @OneToMany(targetEntity ="PartialProcedure", mappedBy="owner", fetch="EAGER", nullable=true)
     * @OrderBy({"idState"="ASC"}
     */
    protected ?Collection $endedInners;


    const INNER_PROC_START_ERR = ' уже отмечена';


    public function __construct(string $name, int $idState, Product $product, string $nameAfterEnd, array $inners, ProcedureFactory $factory)
    {
        parent::__construct($name, $idState, $product, $nameAfterEnd);
        $this->inners = new ArrayCollection();
        foreach ($factory->createPartials($inners, $this) as $orderNumber => $partial) {
            $this->partialsKey[$partial->getName()] = $orderNumber;
            $this->inners->add($partial);
            $this->notEndedInners->add($partial);
        }
        $this->endedInners = new ArrayCollection();
    }

    public function end(): AbstractProcedure
    {
        $this->checkInputCondition((bool)$this->innersEnded(), self::INNERS_NOT_FINISHED);
        return parent::end();
    }


    protected function concreteProcStart(?string $partial = null)
    {
        if ($partial) {
            $started_proc = $this->getInnerByName($partial)->start($partial);
            $this->processingInner = $started_proc;
        }
        return $this;
    }


    public function getProcedures(): Collection
    {
        return $this->inners;
    }

    /**
     * @var string $name
     * @return AbstractProcedure|PartialProcedure
     * @throws WrongInputException
     */
    public function getInnerByName(string $name): AbstractProcedure
    {
        $this->checkInputCondition(
            $found = $this->inners[$this->partialsKey[$name] ?? null] ?? null,self::INNER_NOT_FOUND . $name
        );
        return $found;
    }

    public function getInnersCount(): int
    {
        return $this->getProcedures()->count();
    }

    public function getEndedProcedures(): Collection
    {
        return $this->endedInners;
    }

    public function getNotEndedProcedures(): Collection
    {
        return $this->notEndedInners;
    }

    public function getProcessingOrNextProc(): ?AbstractProcedure
    {
        return $this->processingInner ?? $this->notEndedInners->first();
    }

    public function isComposite(): bool
    {
        return true;
    }


    public function innersEnded(): bool
    {
        return $this->inners->count() === $this->endedInners->count();
    }


    function nextProcStart(AbstractProcedure $procedure)
    {
        $this->checkInputCondition(false,' ' . $procedure->getName() . self::INNER_PROC_START_ERR);
    }

    /**
     * @param TIntervalProcedure|PartialProcedure $procedure
     * @return void
     */
    public function processInnerEnd(TIntervalProcedure $procedure)
    {
        $this->processingInner = null;
        $this->notEndedInners->remove(0);
        $this->endedInners->add($procedure);
        $this->notify(self::PROC_CHANGE_STATE);
        $procedure->notify(self::PROC_CHANGE_STATE);
    }
}