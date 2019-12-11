<?php


namespace App\GUI\components\traits;


use Gui\Components\ContainerObjectInterface;

trait TVisualObjectWrapConstruct
{
    public function __construct(string $class, array $defaultAttributes = [], ContainerObjectInterface $parent = null, $application = null)
    {
        parent::__construct($class, $defaultAttributes, $parent, $application);
    }

}