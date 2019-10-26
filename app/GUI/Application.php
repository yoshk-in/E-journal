<?php


namespace App\GUI;


class Application extends \Gui\Application
{
    public function __destruct()
    {
        $this->terminate();
    }

}