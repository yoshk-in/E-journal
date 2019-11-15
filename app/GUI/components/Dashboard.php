<?php


namespace App\GUI\components;


use App\GUI\Debug;
use App\GUI\factories\ButtonFactory;
use App\GUI\factories\LabelFactory;
use App\GUI\GUIManager;
use App\GUI\handlers\GuiStat;
use React\EventLoop\LoopInterface;

class Dashboard
{
    private $bFactory;
    private $app;
    private $offset = 30;
    private $topOffset;
    private $leftOffset;
    private $buttonHeight = 70;
    private $buttonWidth = 300;
    private $loop;
    private $lFactory;
    private $analytic;


    public function __construct(GUIManager $app, LoopInterface $loop, GuiStat $analytic, $bFactory = ButtonFactory::class, $lFactory = LabelFactory::class)
    {
        $this->bFactory = $bFactory;
        $this->app = $app;
        $this->loop = $loop;
        $this->lFactory = $lFactory;
        $this->analytic = $analytic;
    }

    public function create()
    {
        [, , $width,] = $this->app->getWindowSizes();
        $this->topOffset = $this->offset;
        $this->leftOffset = $width - $this->offset;
        $this->createAddProductButton();
        $this->createSubmitButton();
        $this->createAsyncRest();
    }

    private function createAsyncRest()
    {
        $this->loop->futureTick(function () {
            $this->createStatLayer();
        });
    }

    private function createStatLayer()
    {
        $width = $this->buttonWidth;
        $height = $this->buttonHeight;
        [$left, $top] = $this->updateCreateLine($height, $width);
        $label = $this->lFactory::createBlank($left - 3 * $this->buttonHeight, $top);
//        $label = $this->lFactory::create('текушая статистика: ', 10, $width, $height, $top, $left);
        $this->analytic->attachOutput($label);
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

    private function createElement(string $text, int $elHeight, int $elWidth, \Closure $createMethod)
    {
        [$left, $top] = $this->updateCreateLine($elHeight, $elWidth);
        $createMethod($text, $left, $top, $elHeight, $elWidth);
    }

    private function updateCreateLine(int $elHeight, int $elWidth): array
    {
        $top = $this->topOffset;
        $left = $this->leftOffset - $elWidth;
        $this->topOffset += $this->offset + $elHeight;
        return[$left, $top];
    }


}