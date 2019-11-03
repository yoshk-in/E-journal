<?php


namespace App\events;


use App\domain\AbstractProcedure;

interface ICellSubscriber
{
    public function notify(AbstractProcedure $data);
}