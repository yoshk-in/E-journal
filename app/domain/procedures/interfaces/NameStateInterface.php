<?php


namespace App\domain\procedures\interfaces;


interface NameStateInterface
{
    const READY_TO_START = 0;
    const READY_TO_START_INNER = 1;
    const READY_TO_END_INNER = 2;
    const READY_TO_END = 3;
    const ENDED = 4;

    const STATE_TO_STRING_MAP = [
        self::READY_TO_START => ' еще не начато ',
        self::READY_TO_START_INNER => ' готово к выполнению внутренней процедуры ',
        self::READY_TO_END_INNER => ' готово к завершению внутренней процедуры ',
        self::READY_TO_END => ' готово к завершению ',
        self::ENDED => ' завершено '
    ];

    public function getName(): string;
    public function getStateName(): string;
    public function isEnded(): bool;
    public function isStarted(): bool;
    public function start(?string $innerName);
    public function end(?string $innerName);
    public function getStart(): ?\DateTimeInterface;
    public function getEnd(): ?\DateTimeInterface;

}