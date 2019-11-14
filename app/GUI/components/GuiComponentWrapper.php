<?php


namespace App\GUI\components;


use App\GUI\Debug;
use Gui\Components\ContainerObjectInterface;
use Gui\Components\VisualObjectInterface;

abstract class GuiComponentWrapper
{
    protected $component;
    protected $propertyContainer = [];

    public function __construct(array $defaultAttributes = [], ContainerObjectInterface $parent = null, $application = null)
    {
        $this->createComponent($this->componentClass(), $defaultAttributes, $parent, $application);
        $this->propertyContainer = $defaultAttributes;
    }


    public function getComponent(): VisualObjectInterface
    {
        return $this->component;
    }

    final public function __call($name, $arguments)
    {
        $getSet = substr($name, 0, 3);
        $rest = lcfirst(substr($name, 3));
        switch ($getSet) {
            case 'get':
                if (key_exists($rest, $this->propertyContainer)) {
                    return $this->propertyContainer[$rest];
                }
                return $this->callComponent($name, $arguments);
            case 'set':
                $this->callComponent($name, $arguments);
                $this->propertyContainer[$rest] = $argument = $arguments[0];
                return $this;
        }
        throw new \Exception('call undefined method');
    }

    public function on(string $event, \Closure $closure)
    {
        $this->component->on($event, $closure);
    }

    protected function callComponent($name, $arguments)
    {
        if (method_exists($this->component, $name)) {
            return $this->component->$name(...$arguments);
        }
        throw new \Exception('call undefined method');
    }


    protected function createComponent(string $class, array $defaultAttributes, ContainerObjectInterface $parent = null, $application = null)
    {
        $this->component = new $class($defaultAttributes, $parent, $application);
    }

    abstract protected function componentClass(): string;
}