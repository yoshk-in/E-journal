<?php


namespace App\CLI\parser;


use App\base\AppCmd;

class ConcreteProductInfo extends Parser
{

    protected function doParse()
    {
        $this->request->addCmd(AppCmd::CONCRETE_PRODUCT_INFO);
    }


}