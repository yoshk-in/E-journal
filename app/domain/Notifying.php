<?php


namespace App\domain;


trait Notifying
{
    static $format_time = "Y-m-d H:i";

    protected function notify(Procedure $procedure) : string
    {
        $info = $procedure->getProduct()->getId();
        $info .= " " . $this->getProcedureInfoString($procedure);
        return $info;
    }

    static function getProcedureInfoString($procedure, ?string $short = null) : string
    {
        return $procedure->getInfo($short) . ' ';
    }


    public static function getClassTableData(): array
    {
        $currentProcId = 'currentProcId';
        $id = 'id';
        $procedure_list = static::getProcedureList();
        return array(count($procedure_list), $currentProcId, $id);
    }
}