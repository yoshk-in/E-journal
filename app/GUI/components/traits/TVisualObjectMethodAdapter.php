<?php


namespace App\GUI\components\traits;


use Gui\Components\Label;

trait TVisualObjectMethodAdapter
{
    protected $adaptableMethods = [
        Label::class => 'Text'
    ];


    public function setValue($value)
    {
        return $this->adapt('set',__FUNCTION__, $value);
    }

    public function getValue()
    {
        return $this->adapt('get',__FUNCTION__);
    }

    protected function getAdaptableMethod(string $componentClass): string
    {
        return $this->adaptableMethods[$componentClass];
    }

    protected function isAdaptable()
    {
        return isset($this->adaptableMethods[$class = get_class($this->getComponent())]) ? $class : false;
    }

    protected function adapt($setGet, $methodName, $value = null)
    {
        return ($class = $this->isAdaptable()) ?
            $this->component->{$setGet . $this->getAdaptableMethod($class)}($value)
            :
            $this->component->{$methodName}($value);
    }
}