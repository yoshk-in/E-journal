<?php


namespace App\GUI;


use App\GUI\components\Cell;
use App\GUI\tableStructure\TableRow;
use Gui\Components\InputText;
use Gui\Components\VisualObjectInterface;

class UserActionMng
{
    private ?ClickHandler $currentStrategy;

    public function __construct()
    {
        $this->currentStrategy = null;
    }

    public function changeHandler(ClickHandler $strategy): void
    {
       $this->currentStrategy = $strategy;
    }

    public function handleRow(TableRow $row)
    {
        $this->currentStrategy->handle($row);
    }

    public function handleInputNumber(VisualObjectInterface $object)
    {
        $this->currentStrategy->handleInputNumber($object);
    }




}