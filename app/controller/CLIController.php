<?php

namespace App\controller;


use App\base\CLIRequest;
use App\CLI\parser\CLIParser;


class CLIController implements IController
{
    use TChainOfResponsibility;
    
    private $consoleParser;   
    private $request;

    public function __construct(CLIParser $consoleParser, CLIRequest $request) {

        $this->consoleParser = $consoleParser;
        $this->request = $request;        
    }

    public function run()
    {
        $this->consoleParser->parse($this->request);
        $this->next->run();
    }

   
}
