<?php


namespace App\base\exceptions;


class ExceptionGenerator
{
    public function ensureRighInput(bool $condition, $msg = null)
    {
        if (!$condition) {
            throw new WrongInputException(
                'ошибка: операция не выполнена ' . $msg
            );
        }
    }

    public function ensureRightLogic($conditions, string $msg = null)
    {
        if (!$conditions) {
            throw new WrongModelException(
                'have mistake in domain logic program: ' . $msg
            );
        }
    }
}