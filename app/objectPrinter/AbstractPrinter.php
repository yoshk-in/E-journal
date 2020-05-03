<?php


namespace App\objectPrinter;


use App\CLI\render\Format;
use App\domain\AbstractProduct;
use App\domain\procedures\AbstractProcedure;
use App\domain\procedures\interfaces\NameStateInterface;

class AbstractPrinter implements Format
{
    const START_PREFIX = ' время начала: ';
    const END_PREFIX = ' время завершения:  ';
    const CURRENT_STATE_PATTERN = ' текущее состояние ';


    public static function printAny(NameStateInterface $object): string
    {
        switch (true) {
            case ($object instanceof AbstractProduct):
                return ProductPrinter::print($object);
            case ($object instanceof AbstractProcedure):
                return ProcedurePrinter::print($object);
        }
        return 'unknown printing entity';
    }


    public static function print(NameStateInterface $object)
    {
        $objectProps = self::composePattern(static::getObjectProperties($object));
        $start = self::timeToString($object->getStart(), static::START_PREFIX);
        $end = self::timeToString($object->getEnd(), static::END_PREFIX);
        return $objectProps . ' ' . $start . ' ' . $end . PHP_EOL;
    }



    public static function getObjectProperties(NameStateInterface $object): array
    {
        return [$object->getName()][$object->getStateName()];
    }

    public static function timeToString(?\DateTimeInterface $time, string $prefix = ''): string
    {
        return $prefix . (is_null($time) ? ' нет отметки ' : $time->format(self::TIME));
    }

    public static function composePattern(array $args = []): string
    {
        return sprintf(static::PRINTING_PATTERN . self::CURRENT_STATE_PATTERN, ...$args);
    }
}