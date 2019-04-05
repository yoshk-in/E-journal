<?php

namespace domain;

abstract class DomainObject
{
    /**
     * @Column(type="integer")
     **/
    protected $partNumber;

    /**
     *  @Id
     *  @Column(type="integer")
     *  @GeneratedValue(strategy="CUSTOM")
     *  @CustomIdGenerator(class="\cfg\G9IdGenerator")
     **/

    protected $number;
    protected $fullNumber;

    /** @Column(type="integer") **/
    protected $statement;
    protected $statesArray = [
        'prozvonka'      => 0,
        'nastroyka'      => 1,
        'vibroprochnost' => 2,
        'progon'         => 3,
        'moroz'          => 4,
        'jara'           => 5,
        'mechanikaOTK'   => 6,
        'electrikaOTK'   => 7,
        'mechanikaPZ'    => 8,
        'electrikaPZ'    => 9,
    ];

    public function __construct()
    {
        $this->partNumber = $partNumber;
        $this->number     = $number;
        $this->fullNumber = (int) ((string) $this->partNumber . (string) $this->number);
    }

    public function getNumber()
    {
        return $this->$fullNumber;
    }
}
