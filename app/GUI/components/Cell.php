<?php


namespace App\GUI\components;


use App\GUI\Color;
use Gui\Components\ContainerObjectInterface;
use Gui\Components\Shape;
use App\GUI\CellRow;


class Cell extends GuiComponentWrapper
{

    private $clickCounter = 0;
    private $owner;
    private $defaultBorderColor;
    private $clickBlock = false;


 public function __construct(array $defaultAttributes = [], ContainerObjectInterface $parent = null, $application = null, string $defaultBorderColor = Color::WHITE)
 {
     parent::__construct($defaultAttributes, $parent, $application);
     $this->defaultBorderColor = $defaultBorderColor;
 }

    /**
     * @return mixed
     */
    public function getClickCounter()
    {
        return $this->clickCounter;
    }


    public function plusClickCounter(): void
    {
        ++$this->clickCounter;
    }

    public function resetClickCounter()
    {
        $this->defaultBorderColor();
        $this->clickCounter = 0;
    }

    /**
     * @return mixed
     */
    public function getOwner(): CellRow
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner(CellRow $owner): void
    {
        $this->owner = $owner;
    }


    public function defaultBorderColor()
    {
        $this->component->setBorderColor($this->defaultBorderColor);
    }

    public function getData()
    {
        return $this->getOwner()->getData();
    }


    public function blockClick(bool $bool)
    {
        $this->clickBlock = $bool;
    }

    public function isBlock(): bool
    {
        return $this->clickBlock;
    }


    protected function componentClass(): string
    {
        return Shape::class;
    }
}