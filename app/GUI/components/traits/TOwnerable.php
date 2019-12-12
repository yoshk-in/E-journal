<?php


namespace App\GUI\components\traits;



trait TOwnerable
{
    private $owner;

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

}