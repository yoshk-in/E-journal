<?php

namespace App\domain;

use Doctrine\Common\Collections\Collection;
use DateTimeImmutable;
use DateInterval;

/**
 * @Entity
 *
 **/
class GNine extends Product
{
    /**
     * @Column(type="integer")
     **/
    protected $currentTTProcId;
    /**
     *
     * @OneToMany(targetEntity="G9Procedure", mappedBy="product", cascade="persist")
     **/
    protected $procsCollection;
    /**
     *
     * @OneToMany(targetEntity="G9TechProcedure", mappedBy="product", cascade="persist")
     **/
    protected $ttCollection;



    protected $procedures = [
        'nastroy',
        'technicalTraining',
        'electrikaOTK',
        'electrikaPZ'
    ];

    protected $ttProcedureRules = [
        'vibro' => 'PT30M',
        'progon' => 'PT2H',
        'moroz' => 'PT2H',
        'jara' => 'PT2H'
    ];

    protected $relaxProcedure = [
        'climaticRelax' => 'PT2H'
    ];
    protected $proceduresRules = [
        'minTime' => 'PT30M'
    ];

    protected $climaticProcs = [
        'moroz',
        'jara'
    ];


    public function __construct()
    {
        $this->compositeProcs = ['technicalTraining'];
        foreach ($this->compositeProcs as $composite) {
            $this->ensureRightLogic(
                in_array($composite, $this->procedures),
                '$this->compositeProcs must be equals "technicalTraining"'
            );
        }
        $this->ensureRightLogic(
            !is_null($this->climaticProcs), 'climatic tests are required'
        );
        foreach ($this->climaticProcs as $climatic) {
            $this->ensureRightLogic(
                in_array($climatic, array_keys($this->ttProcedureRules)),
                'wrong name climatic'
            );
        }
        parent::__construct();
    }

    public function initByNumber(int $number): void
    {
        parent::initByNumber($number);
        $this->currentTTProcId = 0;
    }

    public function startTTProcedure(string $name): void
    {
        $next_procedure = $this->getProcByName($name, $this->ttCollection);
        $this->checkNewTTProc($next_procedure);
        if ($this->isClimatic($name)) {
            $this->checkTTRelax($next_procedure);
        }
        $next_procedure->setInterval($this->ttProcedureRules[$name]);
        $next_procedure->setStart();
        $this->currentTTProcId = $next_procedure->getStageId();
    }

    public function getTTCollection(): Collection
    {
        return $this->ttCollection;
    }

    protected function checkTTisFinish(
        Collection $collection, array $arrayOfComposite
    ): void
    {
        $err_msg = '- не отмечены частично или полностью входящие' . '
         в данную процедуры испытания';
        foreach ($collection as $procedure) {
            $this->ensureRightInput($procedure->isFinished(), $err_msg);
        }
    }

    protected function getPrevClimatic(string $next_procedure): string
    {
        $climatic_array = $this->climaticProcs;
        $callback_filter = function ($climatic) use ($next_procedure) {
            if ($climatic === $next_procedure) {
                return false;
            }
            return true;
        };
        $prev_climatic = array_values(
            array_filter($climatic_array, $callback_filter)
        );
        return $prev_climatic[0];
    }

    protected function getProcByName(
        string $procedureName,
        Collection $procedureCollection
    ): Procedure
    {
        foreach ($procedureCollection as $procedure) {
            if ($procedure->getName() === $procedureName) {
                return $procedure;
            }
        }
        $this->ensureRightLogic(false, 'wrong name procedure');
    }

    protected function checkNewTTProc(G9TechProcedure $procedure): void
    {
        $procedure_name = $procedure->getName();
        $this->ensureRightLogic(
            $this->isCompositeProc($this->getCurrentProc()),
            'it is must be compositeProcedure'
        );
        $this->ensureRightLogic(
            array_search($procedure_name, array_keys($this->ttProcedureRules))
            !== false,
            'wrong name'
        );
        $current_tt_proc = $this->ttCollection[$this->currentTTProcId];
        if (!is_null($current_tt_proc)) {
            $this->ensureRightInput(
                $current_tt_proc->isFinished(),
                ' - предыдущая процедура еще не завершена'
            );
        }
    }

    protected function checkTTRelax(G9TechProcedure $procedure): void
    {
        $prev_climatic_name = $this->getPrevClimatic($procedure->getName());
        $prev_climatic
            = $this->getProcByName($prev_climatic_name, $this->ttCollection);
        if ($prev_climatic->getStart() !== null) {
            $now_time = new DateTimeImmutable('now');
            $relax_period = new DateInterval($this->relaxProcedure['climaticRelax']);
            $relax_end = ($prev_climatic->getEnd())->add($relax_period);
            $this->ensureRightLogic(
                $now_time > $relax_end,
                '- не соблюдается перерыв между жарой и морозом'
            );
        }
    }

    protected function isClimatic(string $name): bool
    {
        if (in_array($name, $this->climaticProcs)) {
            return true;
        }
        return false;
    }

    protected function getTargetProcNames(): array
    {
        return [G9Procedure::class, G9TechProcedure::class];
    }

}

