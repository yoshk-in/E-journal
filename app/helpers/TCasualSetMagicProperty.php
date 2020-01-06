<?php


namespace App\helpers;


trait TCasualSetMagicProperty
{

    public function setMagicProperty($name, $value)
    {
        if (property_exists($this, $name)) return $this->$name = $value;
        return null;
    }


}