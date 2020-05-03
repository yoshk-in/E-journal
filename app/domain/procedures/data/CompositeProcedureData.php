<?php


namespace App\domain\procedures\data;


use App\domain\procedures\CompositeProcedure;
use App\domain\procedures\factories\PartialProcedureFactory;
use App\domain\procedures\decorators\ProcedureOwnerDecorator;

class CompositeProcedureData extends AbstractProcedureData
{
    protected static PartialProcedureFactory $partialFactory;

    /** @var string|ProcedureOwnerDecorator */
    const OWNER_STRATEGY = ProcedureOwnerDecorator::class;

    public function createInners(CompositeProcedure $compositeProcedure): \Generator
    {
        $factory = self::$partialFactory;
        $savePrevOwnerData = self::$ownerData;
        self::setOwnerData($compositeProcedure, self::OWNER_STRATEGY);
        yield from $factory->create($compositeProcedure);
        self::$ownerData = $savePrevOwnerData;
    }



    public static function setPartialFactory(PartialProcedureFactory $partialFactory): void
    {
        self::$partialFactory = $partialFactory;
    }

}