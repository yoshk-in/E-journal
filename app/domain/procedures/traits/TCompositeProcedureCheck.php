<?php


namespace App\domain\procedures\traits;


use App\base\exceptions\ProcedureException;
use App\base\exceptions\WrongInputException;

trait TCompositeProcedureCheck
{

    protected function endCheck()
    {
        parent::endCheck();
        $this->checkInnersEnded();
    }

    protected function checkInnersEnded()
    {
        ProcedureException::check((bool)$this->innersEnded(), ' внутренние процедуры данного события не завершены:');
    }

    protected function innerNotFoundException()
    {
        WrongInputException::create('inner not found');
    }
    
    protected function innerNotEndedException()
    {
        ProcedureException::create('внутренняя процедура ' . $this->getProcessingProc()->getName() . 'не завершена');
    }
}