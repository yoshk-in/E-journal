<?php


namespace App\domain\procedures\traits;


use App\base\exceptions\ProcedureException;
use App\domain\procedures\AbstractProcedure;
use App\domain\procedures\CasualProcedure;

trait TProcedureCheck
{
    protected static array $stateToString = [
        CasualProcedure::READY_TO_START => ' еще не начата',
        CasualProcedure::ENDED => ' завершена',
        CasualProcedure::READY_TO_START_INNER => ' уже начата',
        CasualProcedure::READY_TO_END => ' уже начата',
        -1 => ' ? '
    ];


    public function checkCurrentState(int $validState = -1)
    {
        if ($this->procedure->getState() !== $validState) {
            $toAnalyze = new \stdClass();
            $toAnalyze->currentState = 'состояние процедуры: ' . self::$stateToString[$this->procedure->getState()];
            $toAnalyze->validState = ', ожидаемое состояние - ' . self::$stateToString[$validState] . PHP_EOL;
            $toAnalyze->result = $toAnalyze->currentState . $toAnalyze->validState;
            $this->analyze($toAnalyze->result);

        }
    }

    /**
     * @param null $msg
     * @throws ProcedureException
     * @throws \App\base\exceptions\ObjectStateException
     * @throws \App\base\exceptions\ProductException
     */
    protected function analyze($msg = '')
    {
        ProcedureException::create($this->procedure, $msg);
    }


}