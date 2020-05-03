<?php

namespace App\base\exceptions;

use App\domain\AbstractProduct;
use App\domain\procedures\AbstractProcedure;
use Exception;

class ObjectStateException extends AppException
{

    public function additionalMessage(string $msg)
    {
        $this->message .=$msg;
    }

    public static function create($objectMessage = '', $additionalMessage = '')
    {
        switch (true)
        {
            case $objectMessage instanceof AbstractProduct:
                $exception = new ProductException();
                break;
            case $objectMessage instanceof AbstractProcedure:
                $exception = new ProcedureException();
                break;
            default:
                $exception= new self($objectMessage);
        }
        $exception->composeMessage($objectMessage);
        $exception->additionalMessage(self::TAIL . $additionalMessage);
        throw $exception;
    }

    protected function composeMessage($object)
    {
        $this->message = static::TITLE . $this->message;
    }


    public static function check($condition, $msg = '', $addMsg = '')
    {
        if (!$condition) {
            static::create($msg, $addMsg);
        }
    }

    public function dumpObject($object)
    {
        $this->additionalMessage(print_r($object, true));
    }








}
