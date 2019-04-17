<?php

namespace App\domain;

//use App\base\AppHelper;

abstract class DomainObject
{
//    /**
//     * @Column(type="integer")
//     **/
//    protected $partNumber;

    /**
     *  @Id
     *  @Column(type="integer")
     *  @GeneratedValue
     **/

    protected $number;
//    protected $fullNumber;

    /** @Column(type="integer") **/
    protected $statement;
    protected $statesArray = [
        'writeInBD'      => 0,
        'prozvon'        => 1,
        'nastroy'        => 2,
        'vibro'          => 3,
        'progon'         => 4,
        'moroz'          => 5,
        'jara'           => 6,
        'mechanikaOTK'   => 7,
        'electrikaOTK'   => 8,
        'mechanikaPZ'    => 9,
        'electrikaPZ'    => 10,
        'sklad'          => 11
    ];

    public function __construct(int $number)
    {
//        if (is_null($partNumber))  $this->partNumber = (AppHelper::getCacheObject())->getPartNumber();
//        if (!is_null($partNumber)) $this->partNumber = $partNumber;
        $this->number = $number;
//        $this->fullNumber = (int) ((string) $this->partNumber . (string) $this->number);

    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setStatement(string $statement)
    {
        $this->statement = $this->statesArray[$statement];
    }

    public function getStatement()
    {
        return $this->statement;
    }
}
