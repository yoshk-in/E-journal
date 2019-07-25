<?php

namespace App\domain;

use App\base\exceptions\WrongModelException;
use Doctrine\Common\Collections\Collection;
use DateTimeImmutable;
use DateInterval;

/**
 * @Entity
 *
 **/
class GNine extends Product
{
    use Notifying;
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


    protected static $procedures = [
        'nastroy' => ['настройка', 'прическа'],
        'technicalTraining' => ['техтренировка', 'механика ОТК'],
        'electrikaOTK' => ['электрика ОТК', 'механика ПЗ'],
        'electrikaPZ' => ['электрика ПЗ', 'склад']
    ];

    protected static $ttProcedureRules = [
        'vibro' => ['PT1S', 'вибро'],
        'progon' => ['PT1S', 'прогон'],
        'moroz' => ['PT1S', 'мороз'],
        'jara' => ['PT1S', 'жара']
    ];


    protected $relaxProcedure = [
        'climaticRelax' => 'PT1S'
    ];

    protected $proceduresRules = [
        'minTime' => 'PT1S'
    ];

    protected $climaticProcs = [
        'moroz',
        'jara'
    ];

    protected $compositeProcs = ['technicalTraining'];

    public function __construct()
    {
        $this->ensureGNineLogicInit();
        parent::__construct();
    }

    public function getCompositeProc(): array
    {
        return $this->compositeProcs;
    }

    public function initByNumber(int $number): void
    {
        parent::initByNumber($number);
        $this->currentTTProcId = 0;
    }

    public function startTTProcedure(string $name) : string
    {
        $next_procedure = $this->getProcByName($name, $this->ttCollection);
        $this->checkNewTTProc($next_procedure);
        if ($this->isClimatic($name)) {
            $this->checkTTRelax($next_procedure);
        }
        $next_procedure->setInterval($this->getTTProcedureList()[$name]);
        $next_procedure->setStart();
        $this->currentTTProcId = $next_procedure->getStageId();
        return $this->notify($next_procedure);
    }

    public function getTTCollection(): Collection
    {
        return $this->ttCollection;
    }

    protected function getCurrentTTProc(): G9Procedure
    {
        return $this->ttCollection[$this->currentTTProcId];
    }

    protected function checkTTisFinish(Collection $collection, array $arrayOfComposite) : void
    {
        $err_msg = '- не отмечены частично или полностью входящие в данную процедуры испытания';
        foreach ($collection as $procedure) {
            $this->ensureRightInput($procedure->isFinished(), $err_msg);
        }
    }

    protected function getPrevClimatic(string $next_procedure): string
    {
        $climatic_array = $this->climaticProcs;
        $callback_filter = function ($climatic) use ($next_procedure) {
            if ($climatic === $next_procedure) return false;
            return true;
        };
        $prev_climatic = array_values(array_filter($climatic_array, $callback_filter));
        return $prev_climatic[0];
    }

    protected function getProcByName(string $procedureName,Collection $procedureCollection): Procedure
    {
        foreach ($procedureCollection as $procedure) {
            if ($procedure->getName() === $procedureName) {
                return $procedure;
            }
        }
        $this->ensureRightLogic(self::THROW_EXCEPT, 'wrong name procedure');
    }

    protected function checkNewTTProc(G9TechProcedure $procedure): void
    {
        $procedure_name = $procedure->getName();
        $this->ensureRightLogic(
            $this->isCompositeProc($this->getCurrentProc()),
            'it is must be compositeProcedure'
        );
        $this->ensureRightLogic(
            array_search($procedure_name, array_keys($this->getTTProcedureList()))
            !== false,
            'wrong name'
        );
        $prev_tt_proc_is_finished = $this->ttCollection->forAll(function ($key, $tt_proc) {
            $result = $tt_proc->getStart() ? $tt_proc->isFinished() : true;
            return (bool)$result;
        });
        $this->ensureRightInput($prev_tt_proc_is_finished, ' - предыдущая процедура еще не завершена');
    }


    protected function checkTTRelax(G9TechProcedure $procedure): void
    {
        $prev_climatic_name = $this->getPrevClimatic($procedure->getName());
        $prev_climatic = $this->getProcByName($prev_climatic_name, $this->ttCollection);
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
        if (in_array($name, $this->climaticProcs)) return true;
        return false;
    }

    protected function getTargetProcNames(): array
    {
        return [G9Procedure::class, G9TechProcedure::class];
    }

    protected function ensureGNineLogicInit()
    {
        foreach ($this->compositeProcs as $composite) {
            $this->ensureRightLogic(
                in_array($composite, $this->getProcedureList()),
                '$this->compositeProcs must be equals "technicalTraining"'
            );
        }
        $this->ensureRightLogic(
            !is_null($this->climaticProcs), 'climatic tests are required'
        );
        foreach ($this->climaticProcs as $climatic) {
            $this->ensureRightLogic(
                in_array($climatic, array_keys($this->getTTProcedureList())),
                'wrong name climatic'
            );
        }
    }

}

