<?php


namespace App\GUI\startMode;


use App\GUI\GUIManager;

abstract class StartMode
{

    protected $app;

    public function __construct(GUIManager $app)
    {
        $this->app = $app;
    }

    abstract public function run();
}