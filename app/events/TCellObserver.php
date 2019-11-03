<?php


namespace App\events;


use App\domain\AbstractProcedure;
use App\GUI\State;

trait TCellObserver
{

    public function notify(AbstractProcedure $proc)
    {
        $this->setBackgroundColor($color = State::COLOR[$state = $proc->getState()]);
        if ($state === AbstractProcedure::STAGE['end']) $this->getOwner()->nextActiveCell($this, $color);
        elseif ($state === AbstractProcedure::STAGE['start']) $this->getOwner()->setActiveCell($this, $color);
    }
}