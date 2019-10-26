<?php


namespace App\GUI;


class Shape extends \Gui\Components\Shape
{
    private $clickCounter = 0;
    private $owner;

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

    /**
     * @return mixed
     */
    public function getOwner(): RowShapeFactory
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner(RowShapeFactory $owner): void
    {
        $this->owner = $owner;
    }

}