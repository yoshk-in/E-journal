<?php


namespace App\domain\procedures\interfaces;


use App\domain\AbstractProduct;
use App\domain\procedures\decorators\OwnerDecorator;

interface ProcedureInterface extends NameStateInterface
{

    public function getState(): int;
    public function getMark(): ?int;
    public function getOwnerStrategy(): OwnerDecorator;
    public function getOwnerOrder(): int;
    public function getProductOrder(): int;
    public function getProduct(): AbstractProduct;
}