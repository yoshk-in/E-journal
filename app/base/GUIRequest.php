<?php


namespace App\base;


class GUIRequest extends AbstractRequest
{
    protected $env = AppMsg::GUI;

    public function addBlockNumber(int $number)
    {
        $this->blockNumbers[] = $number;
    }

}