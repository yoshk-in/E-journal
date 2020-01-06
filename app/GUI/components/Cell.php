<?php


namespace App\GUI\components;


use App\GUI\components\traits\TClickCounter;
use App\GUI\grid\style\Style;
use Gui\Components\ContainerObjectInterface;

class Cell extends WrapVisualObject
{
    use TClickCounter;

    private string $defaultBorderColor;
    private bool $clickBlock = false;

    public function __construct(Style $style, ContainerObjectInterface $parent = null, $application = null)
    {
        parent::__construct($style, $parent, $application);
        $this->defaultBorderColor = $style->defaultBorderColor;
    }

    public function default()
    {
        $this->component->setBorderColor($this->defaultBorderColor);
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