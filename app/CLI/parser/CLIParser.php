<?php

namespace App\CLI\parser;

use App\base\CLIRequest;
use App\domain\procedures\ProcedureMap;

class CLIParser
{


    protected string $product;
    private CommandMapParser $parser;


    public function __construct(CommandMapParser $parseResolver)
    {
        $this->parser = $parseResolver;
    }


    public function parse()
    {
        $this->parser->parseByChain();
    }





}
