<?php


namespace App\domain;


trait TBeforeEnd
{
    public function beforeEnd(): \DateInterval
    {
        $now = new \DateTime('now');
        $interval = $now->diff($this->end);
        return $interval;
    }

}