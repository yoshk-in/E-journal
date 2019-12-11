<?php


namespace App\GUI\components;


use App\GUI\Color;
use App\GUI\components\traits\TNestingVisualObject;
use App\GUI\components\traits\TVisualObjectWrapConstruct;
use App\GUI\components\traits\TClickCounter;
use App\GUI\components\traits\TOwnerable;
use Gui\Components\ContainerObjectInterface;
use function App\GUI\offset;
use function App\GUI\size;

class Cell extends WrapVisualObject
{
    use TOwnerable;
    use TClickCounter;
    use TVisualObjectWrapConstruct {
        TVisualObjectWrapConstruct::__construct as visualObjectConstruct;
    }
    use TNestingVisualObject;

    private $defaultBorderColor;
    private $clickBlock = false;

    public function __construct(string $class,
                                array $defaultAttributes = [],
                                ContainerObjectInterface $parent = null,
                                $application = null,
                                string $defaultBorderColor = Color::WHITE)
    {
        self::visualObjectConstruct($class, $defaultAttributes, $parent, $application);
        $this->defaultBorderColor = $defaultBorderColor;
    }

    public function default()
    {
        $this->component->setBorderColor($this->defaultBorderColor);
    }

    public function getData()
    {
        return $this->getOwner()->getData();
    }


    public function getOffsets(): array
    {
        return offset($this->getLeft(), $this->getTop());
    }

    public function getSizes(): array
    {
        return size($this->getWidth(), $this->getHeight());
    }


    public function blockClick(bool $bool)
    {
        $this->clickBlock = $bool;
    }

    public function isBlock(): bool
    {
        return $this->clickBlock;
    }



}