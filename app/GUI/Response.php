<?php


namespace App\GUI;


class Response
{
    private $info = [];


    public function getInfo()
    {
        return $this->info;
    }


    public function addInfo($info): void
    {
        $this->info[] = $info;
    }
}