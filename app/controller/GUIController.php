<?php


namespace App\controller;


use App\base\GUIRequest;
use App\GUI\GUIController as gui;

class GUIController implements IController
{
    use TChainOfResponsibility;

    protected $request;
    private $GUIManager;


    public function __construct(
        GUIController $GUIManager,
        GUIRequest $request
    ) {        
        $this->request = $request;
        $this->GUIManager = $GUIManager;
    }


    public function run()
    {
        $this->GUIManager->run($this->next);
    }

}