<?php

namespace App\domain;

use App\base\AppException;

abstract class DomainObject
{
    /**
     *  @Id
     *  @Column(type="integer")
     **/
    protected $number;

    public function __construct(int $number)
    {
        $this->number = $number;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function ensure(bool $condition, string $msg = null)
    {
        if (!$condition) throw new AppException('ошибка: операция не выполнена ' . $msg);
    }



}
