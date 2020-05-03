<?php

namespace App\controller;


use App\base\CLIRequest;
use App\base\exceptions\WrongInputException;
use App\CLI\parser\CLIParser;
use App\infoManager\CLIInfoManager;


class CLIController extends AbstractController
{

    private CLIParser $consoleParser;
    private CLIRequest $request;
    private CLIInfoManager $infoManager;


    public function __construct(CLIParser $consoleParser, CLIRequest $request, CLIInfoManager $manager)
    {

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
