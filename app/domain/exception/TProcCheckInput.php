<?php


namespace App\domain\exception;


use App\base\exceptions\WrongInputException;

trait TProcCheckInput
{
    protected function checkInputCondition($condition, $msg = null): ?\Exception
    {
        [$product, $number] = $this->getProduct()->getNameAndNumber();
        if (!$condition) throw new WrongInputException(
            printf("ошибка, операция не выполнена: блок %s, номер %s, процедура '%s': %s \n", $product, $number, $this->getName(), $msg)
        );

        return null;
    }
}