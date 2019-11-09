<?php


namespace App\domain;


interface IBeforeEnd
{
    public function beforeEnd(): \DateInterval;
}