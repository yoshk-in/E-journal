<?php


namespace App\domain\procedures\traits;


use App\domain\procedures\CasualProcedure;
use App\domain\AbstractProduct;

/**
 * Trait TProductProcedure
 * @package App\domain\procedures\traits
 */
trait TProductProcedure
{
    public function getProductName(): string
    {
        return $this->getProduct()->getProductName();
    }

    public function getProductId(): string
    {
        return $this->getProduct()->getProductId();
    }

    public function getProductPreNumber(): string
    {
        return $this->getProduct()->getPreNumber();
    }

    public function getProductCurrentProc(): CasualProcedure
    {
        return $this->getProduct()->getProcessingInner();
    }

}