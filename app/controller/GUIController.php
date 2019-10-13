<?php


namespace App\controller;


use App\base\GUIRequest;
use App\GUI\GUIManager;

class GUIController implements IController
{
    use TChainOfResponsibility;

    protected $request;
    private $GUIManager;


    public function __construct(
        GUIManager $GUIManager,       
        GUIRequest $request
    ) {        
        $this->request = $request;
        $this->GUIManager = $GUIManager;
    }


    public function run()
    {
        $this->GUIManager->run($this->next);
        //$this->next->run($this->request);
    }

}