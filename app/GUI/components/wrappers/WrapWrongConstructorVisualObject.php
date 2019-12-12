<?php


namespace App\GUI\components\wrappers;


use App\GUI\components\WrapVisualObject;
use Gui\Components\ContainerObjectInterface;

class WrapWrongConstructorVisualObject extends WrapVisualObject
{
    protected function createComponent(string $class, array $defaultAttributes, ContainerObjectInterface $parent = null, $application = null)
    {
        $isFloat = isset($defaultAttributes['isFloat']) && ($defaultAttributes['isFloat'] === true) ? true : false;
        $this->component = new $class($isFloat, $defaultAttributes, $parent, $application);
    }
}