<?php


namespace App\events;


class EventSystemSpecialMarkBuffer
{
    private static array $propertyBuffer = [];

    private static array $classPropertyCounter = [];

    private static string $servicedClass;
    private static string $servicedObjectProperty;

    public static function getSpecialMarkByObjectProperty(string $class, string $markedObjectProperty): int
    {
        self::$servicedClass = $class;
        self::$servicedObjectProperty = $markedObjectProperty;

        if (!isset(self::$propertyBuffer[$class])) {
            $mark = self::setNewMark(self::$classPropertyCounter[$class] = 0);
        } else {
            if (isset(self::$propertyBuffer[$class][$markedObjectProperty]))  {
                $mark = self::getExistingMark();
            } else {
                $mark = self::setNewMark(++self::$classPropertyCounter[$class]);
            }
        }

        return $mark;
    }


    protected static function setNewMark(int $mark): int
    {
        return self::$propertyBuffer[self::$servicedClass][self::$servicedObjectProperty] = $mark;
    }

    protected static function getExistingMark(): int
    {
        return self::$propertyBuffer[self::$servicedClass][self::$servicedObjectProperty];
    }
}