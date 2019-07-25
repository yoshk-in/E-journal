<?php


namespace App\domain;

use DateInterval;


interface TechProcedure
{

    public function setStart(): void;


    public function setInterval(string $interval): void;


    public function getInterval(): DateInterval;


    public function isFinished(): bool;


    public function getInfo(?string $short = null): string;

}