<?php


namespace App\GUI\startMode;


use App\GUI\GUIManager;
use App\GUI\Response;
use Gui\Application;

abstract class StartMode
{

    protected $app;

    public function __construct(GUIManager $app)
    {
        $this->app = $app;
    }

    abstract function run(Response $response, Application $gui);
}