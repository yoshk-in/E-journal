<?php


namespace App\GUI;


use App\base\AppMsg;

class Response
{
    private $info = [];
    private $responseType = AppMsg::GUI_INFO;

    public function getInfo()
    {
        return $this->info;
    }


    public function addInfo($info): void
    {
        $this->info[] = $info;
    }

    public function reset()
    {
        $this->info = [];
        $this->responseType = AppMsg::GUI_INFO;
    }

    public function notFound()
    {
        $this->responseType = AppMsg::NOT_FOUND;
    }

    public function getType()
    {
        return $this->responseType;
    }



}