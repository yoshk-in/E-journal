<?php


namespace App\GUI;


class Response
{
    private $info;


    public function getInfo()
    {
        return $this->info;
    }


    public function setInfo($info): void
    {
        $this->info = $info;
    }
}