<?php


namespace App\GUI\components;


use Gui\Components\ContainerObjectInterface;
use Gui\Components\VisualObjectInterface;

class WrapVisualObject implements VisualObjectInterface
{
    protected $component;
    protected $propertyContainer = [];

    public function __construct(string $class, array $defaultAttributes = [], ContainerObjectInterface $parent = null, $application = null)
    {
        $this->createComponent($class, $defaultAttributes, $parent, $application);
        $this->propertyContainer = $defaultAttributes;
    }


    public function getComponent(): VisualObjectInterface
    {
        return $this->component;
    }

    final public function __call($name, $arguments)
    {
        [$getSet, $rest] = $this->destructNameMethod($name);
        switch ($getSet) {
            case 'get':
                return $this->getMethod($rest, $name, $arguments);
            case 'set':
                return $this->setMethod($rest, $name, $arguments);
        }
        return $this->callComponent($name, $arguments);
    }


    public function fire(string $event)
    {
        $event = 'on' . $event;
        $this->component->fire($event);
    }


    public function destructNameMethod($name): array
    {
        $getSet = substr($name, 0, 3);
        $prop = lcfirst(substr($name, 3));
        return [$getSet, $prop];
    }

    protected function getMethod($prop, $name, array $arguments = [])
    {
        if (key_exists($prop, $this->propertyContainer)) {
            return $this->propertyContainer[$prop];
        }
        return $this->callComponent($name, $arguments);
    }

    protected function setMethod($prop, $name, array $arguments)
    {
        $this->callComponent($name, $arguments);
        $this->propertyContainer[$prop] = $arguments[0];
        return $this;
    }

    protected function callComponent($name, $arguments)
    {
        if (method_exists($this->component, $name)) {
            return $this->component->$name(...$arguments);
        }
        throw new \Exception('call undefined method ' . $name);
    }

    protected function call(string $func, $args = null)
    {
        [$getSet, $prop] =$this->destructNameMethod($func);
        return $this->{$getSet . 'Method'}($prop, $func, [$args]);
    }


    protected function createComponent(string $class, array $defaultAttributes, ContainerObjectInterface $parent = null, $application = null)
    {
        $this->component = new $class($defaultAttributes, $parent, $application);
    }

    public function getAutoSize()
    {
        return $this->call(__FUNCTION__);
    }


    public function setAutoSize($autoSize)
    {
        return $this->call(__FUNCTION__, $autoSize);
    }


    public function getBackgroundColor()
    {
        return $this->call(__FUNCTION__);
    }


    public function setBackgroundColor($color)
    {
        return $this->call(__FUNCTION__, $color);
    }

    public function getBottom()
    {
        return $this->call(__FUNCTION__);
    }


    public function setBottom($bottom)
    {
        return $this->call(__FUNCTION__, $bottom);
    }

    public function getHeight()
    {
        return $this->call(__FUNCTION__);
    }


    public function setHeight($height)
    {
        return $this->call(__FUNCTION__, $height);
    }


    public function getLeft()
    {
        return $this->call(__FUNCTION__);
    }

    public function setLeft($left)
    {
        return $this->call(__FUNCTION__, $left);
    }


    public function getRight()
    {
        return $this->call(__FUNCTION__);
    }


    public function setRight($right)
    {
        return $this->call(__FUNCTION__, $right);
    }


    public function getTop()
    {
        return $this->call(__FUNCTION__);
    }

    public function setTop($top)
    {
        return $this->call(__FUNCTION__, $top);
    }


    public function getWidth()
    {
        return $this->call(__FUNCTION__);
    }

    public function setWidth($width)
    {
        return $this->call(__FUNCTION__, $width);
    }

    public function getVisible()
    {
        return $this->call(__FUNCTION__);
    }

    public function setVisible($visible)
    {
        return $this->call(__FUNCTION__, $visible);
    }
}