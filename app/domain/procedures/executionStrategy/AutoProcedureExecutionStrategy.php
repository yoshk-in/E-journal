<?php


namespace App\domain\procedures\executionStrategy;


use App\base\exceptions\ProcedureException;
use App\domain\procedures\data\AbstractProcedureData;
use App\domain\procedures\proxy\UpdateProcedureProxy;
use Doctrine\DBAL\Schema\Column;
use Doctrine\ORM\Mapping\Entity;

/** @Entity() */
class AutoProcedureExecutionStrategy extends ProcedureExecutionStrategy
{


    /** @Column(type="string", name="`interval`") */
    protected string $interval;

    /** @Column(type="boolean") */
    protected bool $needsToUpdate = false;

    const UPDATE_CHECKER = UpdateProcedureProxy::class;

    public function __construct(AbstractProcedureData $data)
    {
        parent::__construct($data);
        $this->interval = $data->getInterval();
    }

    public function start(&$startTime, &$endTime)
    {
        $this->checkCurrentState(self::PROCEDURE_READY_TO_START);
        $startTime = new \DateTimeImmutable('now');
        $endTime = (clone $startTime)->add(new \DateInterval($this->interval));
        $this->needsToUpdate = true;
        $checker = self::UPDATE_CHECKER;
        new $checker($this->procedure);
        $this->persist();
    }

    /**
     * @param $startTime
     * @param $endTime
     * @throws ProcedureException
     */
    public function end(&$startTime, &$endTime)
    {
        if ($this->needsToUpdate) {
            if ($this->procedure->getState() === self::PROCEDURE_READY_TO_END) {
                if (new \DateTime('now') > $this->procedure->getEnd()) {
                    $this->needsToUpdate = false;
                    $this->persist();
                    return;
                }
            }
        }
        $this->analyze();

    }

    public function needsToUpdate(): bool
    {
        try {
            $this->procedure->end($this->procedure->getProcessingProc()->getName());
            return true;
        } catch (ProcedureException $exception) {
            return false;
        }
    }




    public function beforeEnd(): ?\DateInterval
    {
        if ($this->procedure->isEnded()) return null;
        if ($this->procedure->getStart()) return (new \DateTime('now'))->diff($this->procedure->getEnd());
        return null;
    }
}