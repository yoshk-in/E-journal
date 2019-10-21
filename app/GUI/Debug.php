<?php


namespace App\GUI;


use Gui\Components\Label;

class Debug
{
    static function print($text)
    {
        switch (gettype($text)) {
            case 'string':
                break;
            case 'array':
                $text = implode("\n", $text);
        }
        new Label([
            'text' => $text,
            'top' => 300,
            'fontSize' => 10,
            'left' => 10,

        ]);
    }
}