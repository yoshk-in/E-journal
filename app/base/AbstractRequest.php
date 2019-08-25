<?php


namespace App\base;


abstract class AbstractRequest
{
    protected $data = [];

    protected $feedback = [];

    public function __construct()
    {
        $this->data['cmds'] = [];

    }

    public function addCommand($name)
    {
        $this->data['cmds'][] = $name;
    }


    public function getCommands()
    {
        return $this->getProperty('cmds');
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
            $string .= $message . "\n";
        }
        return $string;
    }
}