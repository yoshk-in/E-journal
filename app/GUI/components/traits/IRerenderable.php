<?php


namespace App\GUI\components\traits;


interface IRerenderable
{
    public function rerender(string $whatRerender, string $value);
}