<?php


namespace App\GUI;



use App\events\ICellSubscriber;
use App\events\TCellObserver;


class Shape extends \Gui\Components\Shape implements ICellSubscriber
{
    use TCellObserver;

    private $clickCounter = 0;
    private $owner;
    private $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Shape
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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