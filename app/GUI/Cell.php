<?php


namespace App\GUI;


use Gui\Components\ContainerObjectInterface;
use Gui\Components\Shape;


class Cell
{

    private $clickCounter = 0;
    private $owner;
    private $shape;
    private $defaultBorderColor;
    private $clickBlock = false;


    public function __construct(

        array $defaultAttributes = [],
        ContainerObjectInterface $parent = null,
        $application = null,
        $defaultBorderColor = Color::WHITE

    ) {

        $this->shape = new Shape($defaultAttributes, $parent, $application);
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
    public function getOwner(): RowCellFactory
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner(RowCellFactory $owner): void
    {
        $this->owner = $owner;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->shape, $name)) {
            return $this->shape->$name(...$arguments);
        }
        throw new \Exception('call undefined method');
    }

    public function defaultBorderColor()
    {
        $this->shape->setBorderColor($this->defaultBorderColor);
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


}