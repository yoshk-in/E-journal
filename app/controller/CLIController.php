<?php

namespace App\controller;


use App\base\CLIRequest;
use App\CLI\parser\CLIParser;
use App\infoManager\CLIInfoManager;


class CLIController implements IController
{
    use TChainOfResponsibility;
    
    private $consoleParser;   
    private $request;
    private $infoManager;


    public function __construct(CLIParser $consoleParser, CLIRequest $request, CLIInfoManager $manager) {

        $this->consoleParser = $consoleParser;
        $this->request = $request;
        $this->infoManager = $manager;
    }

    public function run()
    {
        $this->consoleParser->parse();
        $this->next->run();
        $this->infoManager->dispatch();
    }

   
}
