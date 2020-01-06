<?php


namespace App\GUI\components\traits;


use App\GUI\grid\style\Style;
use Gui\Components\Label;

trait TVisualObjectMethodAdapter
{
    protected array $adaptMethods = [
        Label::class => 'Text'
    ];


    public function setValue($value)
    {
        return $this->adapt('set',__FUNCTION__, $value);
    }

    public function getValue()
    {
        return $this->propertyContainer['value'] ?? $this->adapt('get',__FUNCTION__);
    }

    protected function isAdapt()
    {
        return $this->adaptMethods[get_class($this->getComponent())] ?? false;
    }

    protected function adapt($setGet, $methodName, $value = null)
    {
        return ($class = $this->isAdapt()) ?
            $this->component->{$setGet . $this->adaptMethods[$class]}($value)
            :
            $this->component->{$methodName}($value);
    }

    protected function adaptConstruct(Style $style, $parent, $application)
    {
        $defaultAttributes = $style->getVisualProps();
        !$this->isAdapt() ?: (!$style->value ?: $defaultAttributes['text'] = $style->value);
        $this->createComponent($style->guiComponentClass, $defaultAttributes, $parent, $application);
        $this->propertyContainer = $defaultAttributes;
    }
}