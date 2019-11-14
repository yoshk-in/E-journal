<?php


namespace App\GUI\components;


use App\GUI\factories\ButtonFactory;
use App\GUI\GUIManager;

class Dashboard
{
    private $bFactory;
    private $app;
    private $offset = 30;
    private $topOffset;
    private $leftOffset;
    private $buttonHeight = 70;
    private $buttonWidth = 300;


    public function __construct(GUIManager $app, $bFactory = ButtonFactory::class)
    {
        $this->bFactory = $bFactory;
        $this->app = $app;
    }

    public function create()
    {
        [, , $width,] = $this->app->getWindowSizes();
        $this->topOffset = $this->offset;
        $this->leftOffset = $width - $this->offset;
        $this->createAddProductButton();
        $this->createSubmitButton();
    }

    private function createSubmitButton()
    {
        $on = function () {
            $this->app->update();
        };
        $this->createButton('ЗАПИСАТЬ', $on);
    }

    private function createAddProductButton()
    {
        $on = function () {
            $this->app->addProduct();
        };
        $this->createButton('+   ДОБАВИТЬ  ', $on);
    }

    private function createButton(string $text, \Closure $on)
    {
        $callFactoryMethod = function ($text, $left, $top, $height, $width) use ($on) {
            $this->bFactory::createWithOn($text, $left, $top, $height, $width, $on);
        };
        $this->createElement($text, $this->buttonHeight, $this->buttonWidth, $callFactoryMethod);
    }

    private function createElement(string $text, int $elHeight, $elWidth, \Closure $createMethod)
    {
        $top = $this->topOffset;
        $left = $this->leftOffset - $elWidth;
        $createMethod($text, $left, $top, $elHeight, $elWidth);
        $this->topOffset += $this->offset + $elHeight;
    }
}