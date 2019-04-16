<?php

 namespace domain;

 /** @Entity @Table(name="G9") **/
 class G9 extends DomainObject
 {
    public function __construct(int $number)
    {
        parent::__construct($number);
    }
 }
