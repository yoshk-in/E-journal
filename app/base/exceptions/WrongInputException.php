<?php


namespace App\base\exceptions;


class WrongInputException extends AppException
{
    public static function create($msg = '', array $args = [])
    {
        throw new self(sprintf(self::TITLE . self::TAIL . $msg, $args));
    }
}