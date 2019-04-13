<?php

namespace App\base;

class Request
{
    private $data     = [];
    private $feedback = [];

    public function __construct()
    {
        $syntaxParser = AppHelper::getConsoleSyntaxParser();
        if (!is_null($syntaxParser)) {
            $syntaxParser::parse($this);
        }
    }

    public function setProperty($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function getProperty($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }

    public function setFeedback($value)
    {
        $this->feedback[] = $value;
    }

    public function getFeedback()
    {
        return $this->feedback;
    }

    public function getFeedbackString()
    {
        $string = "";
        foreach ($this->feedback as $message) {
            $string .= $message . "\n" ;
        }
        return $string;
    }

    public function setCommand($name)
    {
        $this->setProperty('cmd', $name);
    }

    public function getCommand()
    {
        return $this->getProperty('cmd');
    }

    public function setPartNumber($name)
    {
        $this->setProperty('partNumber', $name);
    }

    public function getPartNumber()
    {
        return $this->getProperty('partNumber');
    }

    public function setBlockNumbers($name)
    {
        $this->setProperty('blockNumbers', $name);
    }

    public function getBlockNumbers()
    {
        return $this->getProperty('blockNumbers');
    }


}
