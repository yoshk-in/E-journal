<?php


namespace App\domain;

/**
 * @Entity
 **/
class G9TechProcedure extends Procedure
{
    use TechProcedureTrait;

    protected $interval;

    /**
     * @ManyToOne(targetEntity="GNine")
     **/
    protected $product;

}