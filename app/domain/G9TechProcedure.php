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
     * @id
     * @ManyToOne(targetEntity="GNine")
     **/
    protected $product;

}