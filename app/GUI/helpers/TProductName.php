<?php


namespace App\GUI\helpers;


trait TProductName
{
    protected function getProductName(): string
    {
        return $this->requestMng->getProduct();
    }
}