<?php

namespace App\base;

class Request
{
    private $data     = [];
    private $feedback = [];

    public function __construct()
    {
        $syntaxParser = AppHelper::getConsoleSyntaxParser();
        $syntaxParser::parse($this);
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
            $string .= $message;
        }
        return $string;
    }
}
