<?php


namespace App\GUI\startMode;


use App\GUI\components\Dashboard;
use App\GUI\GUIManager;
use App\GUI\ProductTableComposer;


class MainMode extends StartMode
{
    private $tComposer;
    private $dashboard;

    public function __construct(GUIManager $app, ProductTableComposer $tComposer, Dashboard $dashboard)
    {
        parent::__construct($app);
        $this->tComposer = $tComposer;
        $this->dashboard = $dashboard;
    }

    function run()
    {
        $this->tComposer->prepareTable($this->app->getProduct());
        $this->dashboard->create();
    }



}