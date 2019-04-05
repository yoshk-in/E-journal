<?php

namespace App\base;

class Request
{
    private $data = [];

    public function __construct()
    {
        if (isset($_SERVER['argv'][1])) {
            foreach ($_SERVER['argv'] as $arg) {
                if (strpos('=', $arg)) {
                    list($key, $value) = explode('=', $arg);
                    $this->setProperty($key, $value);
                }
                $this->setProperty('cmd', $arg);
            }
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
}
