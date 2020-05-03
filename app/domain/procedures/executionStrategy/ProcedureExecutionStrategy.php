<?php


namespace App\domain\procedures\executionStrategy;


use App\base\exceptions\ProcedureException;
use App\domain\procedures\CasualProcedure;
use App\domain\procedures\CompositeProcedure;
use App\domain\procedures\data\AbstractProcedureData;
use App\domain\procedures\traits\TProcedureCheck;
use App\events\Event;
use App\repository\traits\TDatabase;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * Class ProcedureExecutionStrategy
 * @package App\domain\procedures\procedureExecutionStrategy
 * @Entity()
 * @InheritanceType("SINGLE_TABLE")
 */
abstract class ProcedureExecutionStrategy
{
    use TDatabase, TProcedureCheck;

    /** @var CasualProcedure|CompositeProcedure */
    /**
     * @OneToOne(targetEntity="App\domain\procedures\CasualProcedure", mappedBy="executionStrategy")
     */
    protected CasualProcedure $procedure;
    /**
     * @Id()
     * @GeneratedValue()
     * @Column(type="integer")
     */
    private int $id;

    const PROCEDURE_READY_TO_START = CasualProcedure::READY_TO_START;
    const PROCEDURE_READY_TO_END = CasualProcedure::READY_TO_END;





    public function __construct(AbstractProcedureData $data)
    {
        $this->procedure = $data->getInitializingProcedure();
        $this->persist();
    }



    abstract function start(&$startTime, &$endTime);

    abstract function end(&$startTime, &$endTime);

    abstract public function beforeEnd(): ?\DateInterval;




}