<?php


namespace App\domain;

use DateTimeImmutable;

/**
 * @Entity
 *
 **/
class G9Procedure extends Procedure
{
    use ProcedureTrait;

    /**
     * @Id
     * @ManyToOne(targetEntity="GNine")
     **/
    protected $product;
}
