<?php


namespace App\base\exceptions;



use App\domain\procedures\AbstractProcedure;
use App\domain\procedures\interfaces\ProcedureInterface;
use Throwable;

class ProcedureException extends ProductException
{

    /**
     * @param ProcedureInterface $procedure
     */
    protected function composeMessage($procedure)
    {
//        $this->dumpObject($procedure);
        $product = $this->tryCall([$procedure, 'getProduct']);
        parent::composeMessage($product);
        $this->additionalMessage($procedure);
    }
}