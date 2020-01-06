<?php


namespace App\GUI\components;


use App\GUI\components\traits\TOwnerable;
use App\GUI\components\traits\TVisualObjectMethodAdapter;
use App\GUI\grid\style\Style;
use App\GUI\grid\traits\DelegateInterface;
use App\GUI\grid\traits\TCellDelegator;
use App\GUI\grid\traits\THierarchy;
use App\GUI\tableStructure\TableRow;
use Gui\Components\ContainerObjectInterface;
use Gui\Components\VisualObjectInterface;

/**
 * Class WrapVisualObject
 * @package App\GUI\components
 * @method setRow(TableRow $row)
 * @method TableRow getRow
 */

class WrapVisualObject implements VisualObjectInterface, DelegateInterface
{
    use TCellDelegator, TVisualObjectMethodAdapter, THierarchy, TOwnerable;

    protected VisualObjectInterface $component;
    protected array $propertyContainer = [];


    public function __construct(Style $style, ?ContainerObjectInterface $parent = null, $application = null)
    {
        $this->adaptConstruct($style, $parent, $application);
    }


    public function getComponent(): VisualObjectInterface
    {
        return $this->component;
    }


    public function getMethod($prop, $name, array $arguments = [])
    {
        if (key_exists($prop, $this->propertyContainer)) {
            return $this->propertyContainer[$prop];
        }
        return $this->callComponent($name, $arguments);
    }

    public function setMethod($prop, $name, array $arguments)
    {
        $this->callComponent($name, $arguments);
        $this->propertyContainer[$prop] = $arguments[0];
        return $this;
    }


    public function fire(string $event)
    {
        $event = 'on' . $event;
        $this->component->fire($event);
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

    public function getValue()
    {
        return $this->callComponent(__FUNCTION__, []);
    }

}