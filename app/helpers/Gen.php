<?php


namespace App\helpers;


class Gen
{
    public static function settle(\Generator $generator, iterable $settlers)
    {
        foreach ($settlers as $settler)
        {
            $generator->send($settler);
        }
    }

    public static function spin(\Generator $generator): \Generator
    {
        while ($generator->valid()) {
            $generator->next();
        }
        return $generator;
    }
}