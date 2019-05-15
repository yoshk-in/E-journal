<?php

namespace App\domaini;

use Doctrine\Common\Collections\Collection;

/** @Entity @Table(name="g9s") * */
class GNine extends Product
{
    protected static $procedures = [
        'nastroy',
        'technicalTraining',
        'electrikaOTK',
        'electrikaPZ'
    ];
    protected static $ttProcedureRules = [
        'vibro' => 'PT30M',
        'progon' => 'PT2H',
        'moroz' => 'PT2H',
        'jara' => 'PT2H'
    ];

    protected static $relaxProcedure = [
        'climatic_relax' => 'PT2H'
    ];

    protected static $proceduresRules = [
        'minProcTime' => 'PT30M'
    ];

    protected $currentTTProcId;

    protected static $climaticProcs = [
        'moroz',
        'jara'
    ];


    public function __construct()
    {
        $this->compositeProcs = ['technicalTraining'];
        $this->ensureRightLogic(
            in_array($this->compositeProcs, self::$procedures),
            "{$this->compositeProcs} must be equals 'technicalTraining'"
        );
        $this->ensureRightLogic(
            !is_null(self::$climaticProcs), 'climatic tests are required'
        );
        foreach (self::$climaticProcs as $climatic) {
            $this->ensureRightLogic(
                in_array($climatic, array_keys(self::$ttProcedureRules)),
                'wrong name climatic'
            );
        }
        parent::__construct();
    }

    public function startTTProcedure(string $name) : void
    {
        $next_procedure = $this->getProcByName($name, $this->ttCollection);
        $this->checkNewTTProc($next_procedure);

        if ($this->isClimatic($name)) {
            $this->checkTTRelax($next_procedure);
        }
        $next_procedure->setInterval(self::$ttProcedureRules[$name]);
        $next_procedure->setStartProc(self::$ttProcedureRules[$name]);
        $this->currentTTProcId = $next_procedure->getIdStage();
    }

    protected function checkTTisFinish(
        Collection $collection, array $arrayOfComposite
    ): void {
        $err_msg = '- нет отмечены частично или полностью входящие' . '
         в данную процедуры испытания';
        $this->ensureRightLogic(
            $collection->count() === count($arrayOfComposite), $err_msg
        );
        foreach ($collection as $procedure) {
            $this->ensureRightLogic(!is_null($procedure->getEnd()), $err_msg);
        }
    }

    protected function getPrevClimatic(string $next_procedure): string
    {
        $climatic_array = self::$climaticProcs;
        $callback_filter = function ($climatic) use ($next_procedure) {
            if ($climatic === $next_procedure) {
                return false;
            }
            return true;
        };
        $prev_climatic = array_filter($climatic_array, $callback_filter);

        return $prev_climatic[0];
    }

    protected function getProcByName(
        string $procedureName,
        Collection $procedureCollection
    ) : Procedure {
        foreach ($procedureCollection as $procedure) {
            if ($procedure->getName() === $procedureName) {
                $this->ensureRightLogic(
                    is_null($procedure->getStart()),
                    ' - данная процедура уже отмечена'
                );
            }
            return $procedure;
        }
    }

    protected function checkNewTTProc(Procedure $procedure): void
    {
        $procedure_name = $procedure->getName();
        $this->ensureRightLogic(
            $this->isCompositeProc($procedure_name),
            'it is must be compositeProcedure'
        );
        $this->ensureRightLogic(
            array_search($procedure_name, array_keys(self::$ttProcedureRules)),
            'wrong name'
        );
        $current_tt_proc = $this->ttCollection[$this->currentProcId];
        if (!is_null($current_tt_proc)) {
            $this->ensureRightLogic(
                $current_tt_proc->ifFinised(),
                ' - предыдущая процедура еще не завершена'
            );
        }
    }

    protected function checkTTRelax(Procedure $procedure) : void
    {
        $prev_climatic = $this->getPrevClimatic($procedure->getName());
        $relax_period = new \DateInterval(self::$relaxProcedure['climatic_relax']);
        $prev_climatic = clone $this->ttCollection[$prev_climatic];
        $relax_end = ($prev_climatic->getEnd())->add($relax_period);
        $now_time = new \DateTime('now');
        $this->ensureRightLogic(
            $now_time < $relax_end,
            '- не соблюдается перерыв между жарой и морозом'
        );
    }

    protected function isClimatic(string $name) : bool
    {
        if (in_array($name, self::$climaticProcs)) {
            return true;
        }
        return false;
    }

}

