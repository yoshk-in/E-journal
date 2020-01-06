<?php


namespace App\domain\traits;


trait TChangeNameAfterEndProcedure
{

    /** @Column(type="string") */
    protected string $nameAfterEnd;

    public function __construct(string $name, int $idState, IProcedureOwner $owner, string $nameAfterEnd)
    {
        parent::__construct($name, $idState, $owner);
        $this->nameAfterEnd = $nameAfterEnd;
    }

}