<?php

namespace App\base;

class Request
{
    private $data = [];

    private $feedback = [];

    public function __construct()
    {
        $this->data['cmds'] = [];

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
            $string .= $message."\n";
        }
        return $string;
    }

    public function addCommand($name)
    {
        $this->data['cmds'][] = $name;
    }

    public function getCommands()
    {
        return $this->getProperty('cmds');
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

