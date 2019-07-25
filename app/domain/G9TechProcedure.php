<?php


namespace App\domain;

/**
 * @Entity
 **/
class G9TechProcedure extends Procedure implements TechProcedure
{
    use TechProcedureTrait;

    protected $interval;

    /**
     * @id
     * @ManyToOne(targetEntity="GNine")
     **/
    protected $product;

}