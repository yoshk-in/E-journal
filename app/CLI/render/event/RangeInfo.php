<?php


namespace App\CLI\render\event;



class RangeInfo extends Info
{
    protected $title = 'найдена следующая информация:';

    protected function doRender($reporter)
    {
        $this->output .= $this->formatter->handle($reporter);
    }

}