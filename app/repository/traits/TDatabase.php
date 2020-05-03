<?php


namespace App\repository\traits;


use App\repository\DB;

trait TDatabase
{
    protected function persist()
    {
        DB::persist($this);
    }

    protected function remove()
    {
        DB::remove($this);
    }
}