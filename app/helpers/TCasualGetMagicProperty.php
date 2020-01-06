<?php


namespace App\helpers;


class TCasualGetMagicProperty
{
    public function getMagicProperty($name)
    {
        if (property_exists($this, $name)) return $this->$name;
        return null;
    }
}