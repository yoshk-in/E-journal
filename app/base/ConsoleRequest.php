<?php

namespace App\base;

class ConsoleRequest extends AbstractRequest
{
    public function __construct()
    {
        $this->setConsoleArgs();
        parent::__construct();
    }


    public function addPartialProcName($name)
    {
        $this->data['partial'] = $name;
    }
    public function getPartialProcCommand()
    {
        return $this->data['partial'] ?? $this->data['partial'];

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


    protected function setConsoleArgs()
    {
        $this->data['console_args'] = $_SERVER['argv'];
    }

    public function getConsoleArgs()
    {
        return $this->data['console_args'];
    }

    public function setProductName(string $name)
    {
        $this->data['product_name'] = $name;
    }

    public function getProductName()
    {
        return $this->data['product_name'];
    }
}

