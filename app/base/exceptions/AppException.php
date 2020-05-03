<?php


namespace App\base\exceptions;


class AppException extends \Exception
{
    const TITLE = PHP_EOL . 'ошибка: операция не выполнена, ';
    const TAIL = ' характер ошибки - ';
}