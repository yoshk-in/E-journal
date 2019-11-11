<?php


namespace App\GUI\components;


use Gui\Components\ContainerObjectInterface;
use Gui\Components\VisualObjectInterface;

abstract class GuiComponentWrapper
{
    protected $component;
    protected $top;

    public function __construct(array $defaultAttributes = [], ContainerObjectInterface $parent = null, $application = null) {

        $this->createComponent($this->componentClass(), $defaultAttributes, $parent, $application);
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->component, $name)) {
            return $this->component->$name(...$arguments);
        }
        throw new \Exception('call undefined method');
    }

    public function getComponent(): VisualObjectInterface
    {
        return $this->component;
    }

    public function setTop(int $top)
    {
        $this->top = $top;
        $this->component->setTop($top);
        return $this;
    }

    public function getTop(): int
    {
        return $this->top;
    }

    protected function createComponent(string $class, array $defaultAttributes, ContainerObjectInterface $parent = null, $application = null)
    {
        $this->component = new $class($defaultAttributes, $parent, $application);
    }

    abstract protected function componentClass(): string ;
}