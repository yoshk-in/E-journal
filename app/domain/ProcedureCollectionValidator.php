<?php


namespace App\domain;


use App\base\exceptions\WrongModelException;

trait ProcedureCollectionValidator
{
    /** @postLoad */
    public function checkProcOrder(): ?\Exception
    {
        foreach ($this->procCollection as $key => $proc) {

            if ($proc->getIdState() === $key) continue;
            else throw new WrongModelException(' неправильный порядок загрузки процедур с базы данных;' .
            ' допиши ' . self::class . ' метод ' . __FUNCTION__);

        }
        return null;
    }

}