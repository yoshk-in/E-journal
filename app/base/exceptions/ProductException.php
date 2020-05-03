<?php


namespace App\base\exceptions;



use App\domain\AbstractProduct;
use App\domain\procedures\interfaces\ProcedureInterface;

class ProductException extends ObjectStateException
{

    /**
     * @param AbstractProduct $product
     */
    protected function composeMessage($product)
    {
//        $this->dumpObject($product);
        $this->additionalMessage($product);
        parent::composeMessage(null);
    }

    /**
     * @param callable | array $call
     * @return string | AbstractProduct | ProcedureInterface
     */
    protected function tryCall(array $call)
    {
        [$subject, $method] = $call;
        if (is_string($subject)) return $subject;
        if (is_null($subject)) return '?';
        try {
            return $subject->$method();
        } catch (\Exception $exception) {
            return '?';
        }
    }

}