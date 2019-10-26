<?php


namespace App\CLI\render\event;


use App\CLI\render\Format;

class NotFound extends AbstractInfoDispatcher
{
    protected $title = 'не найдено информации по данным номерам:';

    protected function doRender($reporter)
    {
        $this->output = implode($reporter->getNumbers(), Format::COMMA);
    }

    protected function initFormatter(): void
    {
        // TODO: Implement initFormatter() method.
    }

    public function flush()
    {
        echo $this->title . PHP_EOL;
        echo $this->output . PHP_EOL;
    }
}