<?php


namespace App\domain;


use App\base\AppHelper;
use DateTimeImmutable;

trait Notifying
{
    private $format_time = "Y-m-d H:i:s";

    protected function notify(Procedure $procedure, ?DateTimeImmutable $time = null)
    {
        $destination = $this->getDestination();
        $info = $procedure->getProduct()->getId();
        $info .= $this->getProcedureInfo($procedure, $time);
        $destination->setFeedback($info);
    }

    protected function getDestination()
    {
        return AppHelper::getRequest();
    }

    protected function getProcedureInfo($procedure, $time)
    {
        $name = $procedure->getName();
        $format = $this->format_time;
        $string = ' время';
        if ($procedure instanceof G9Procedure) {
            $string .= ($procedure->getStart() === $time) ?
                ' начала ' . $name . ' ' . $procedure->getStart()->format($format) :
                ' завершения ' . $name . ' ' . $procedure->getEnd()->format($format) ;

        } elseif ($procedure instanceof G9TechProcedure) {
            $string .= ' начала ' . $name . ' ' . $procedure->getStart()->format($format) .
                ' время завершения ' . $procedure->getEnd()->format($format) ;
        }
        return $string;
    }
}