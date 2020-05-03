<?php


namespace App\domain\procedures\traits;


use App\domain\procedures\CasualProcedure;
use App\domain\procedures\interfaces\ProcedureInterface;

interface IProductProcedure
{
    function productIsStarted():bool;
    function productIsEnded():bool;
    public function getProductName(): string;
    public function isDoubleNumber(): bool;
    public function nextMainNumber(): ?int;
    public function getAdvancedNumber();
    public function getNameAndNumber(): array;
    public function setNumbers(int $number, int $advancedNumber);
    public function getNumber(): ?int;
    public function forward();
    public function startProcedure(?string $partial = null);
    public function nextProcStart(ProcedureInterface $proc);
    public function endProcedure();
    public function getCurrentProc(): ProcedureInterface;
    public function getProcessProcFromGenOrder(?string $partial = null);
    public function getProductId(): int;
}