<?php


namespace App\domain\procedures;


use App\domain\procedures\data\AbstractProcedureData;
use App\domain\procedures\executionStrategy\AutoProcedureExecutionStrategy;
use App\domain\procedures\interfaces\ProcedureInterface;
use App\domain\procedures\executionStrategy\ProcedureExecutionStrategy;
use App\domain\procedures\traits\TCasualProcedure;
use App\domain\procedures\traits\TProcedureExecution;
use App\domain\procedures\traits\TProcedureProperties;
use App\domain\procedures\traits\TProductProcedure;
use App\objectPrinter\TPrintingObject;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use App\events\{Event, IEventType, traits\TObservable};

/** @Entity() */
class CasualProcedure extends AbstractProcedure implements ProcedureInterface
{
    use TProductProcedure, TCasualProcedure, TProcedureProperties, TProcedureExecution, TObservable, TPrintingObject;



    const CHANGE_STATE = [
        self::READY_TO_START => self::READY_TO_END,
        self::ENDED => self::ENDED
    ];


    const FORCE_BY_STATE = [
        self::ENDED => 'analyze',
        self::READY_TO_START => 'start',
        self::READY_TO_END => 'end'
    ];

    /** @Column(type="datetime_immutable", nullable=true) */
    protected ?\DateTimeInterface $start = null;
    /** @Column(type="datetime_immutable", nullable=true) */

    protected ?\DateTimeInterface $end = null;

    /** @Column(type="integer", nullable=false) */
    protected int $productOrder;
    /** @Column(type="integer", nullable=false) */
    protected int $state = self::READY_TO_START;


    /** @Column(type="integer", nullable=true) */
    protected ?int $noteMark = null;


    /**
     * @OneToOne(targetEntity="App\domain\procedures\executionStrategy\ProcedureExecutionStrategy", inversedBy="subject")
     */
    /** @var ProcedureExecutionStrategy | AutoProcedureExecutionStrategy */
    protected ProcedureExecutionStrategy $executionStrategy;


    public function __construct(AbstractProcedureData $data)
    {
        $this->initBaseProps($data);
        $this->persist();
    }

    public function setMark(string $noteMark): ProcedureInterface
    {
        $this->noteMark = $noteMark;
        return $this->getFacade();
    }

    public function getMark(): ?int
    {
        return $this->noteMark;
    }


    protected function initBaseProps(AbstractProcedureData $data)
    {
        [$this->productOrder, $this->executionStrategy, $this->ownerStrategy] =  $data->initProcedureData($this);
    }


    public function start(?string $innerName): self
    {
        $this->selfStart();
        return $this;
    }

    public function end(?string $innerName): self
    {
        $this->selfEnd();
        return $this;
    }

    protected function selfEnd()
    {
        $this->executionStrategy->end($this->start, $this->end);
        $this->state = self::ENDED;
        $this->finishOperation(IEventType::END);
    }

    protected function selfStart()
    {
        $this->executionStrategy->start($this->start, $this->end);
        $this->state = $this->stateAfterStart();
        $this->finishOperation(IEventType::START);
    }

    protected function stateAfterStart(): int
    {
        return self::READY_TO_END;
    }


    protected function finishOperation(string $finishType)
    {
        $this->ownerStrategy->ownerHandling();
        $this->persist();
        $this->event($finishType);
    }


    protected function analyze()
    {
        $this->executionStrategy->analyze();
    }

    public function force()
    {
        $force_method = static::FORCE_BY_STATE[$this->getState()];
        $this->$force_method();
    }


}
