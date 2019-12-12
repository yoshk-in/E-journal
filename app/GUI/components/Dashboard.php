<?php


namespace App\GUI\components;

use App\GUI\domainBridge\RequestManager;
use App\GUI\factories\ButtonFactory;
use App\GUI\factories\InputFactoryWrapping;
use App\GUI\factories\LabelFactory;
use App\GUI\GUIManager;
use App\GUI\handlers\Alert;
use App\GUI\handlers\GuiStat;

class Dashboard
{

    protected $bFactory;
    protected $app;
    protected $offset = 30;
    protected $topOffset = 30;
    protected $leftOffset;
    protected $buttonHeight = 70;
    protected $buttonWidth = 300;
    protected $lFactory;
    protected $analytic;
    protected $iFactory;
    protected $productSelect;
    protected $inputText;
    private $requestMng;
    private $alert;
    private $widthInputLabel = 50;
    private $title = 'счетчик от ежемесячной отгрузки продукции:';
    private $titleHeight = 20;
    private $margin = 20;


    public function __construct(
        GUIManager $app,
        GuiStat $analytic,
        ProductSelect $productSelect,
        RequestManager $requestMng,
        Alert $alert,
        ButtonFactory $bFactory,
        LabelFactory $lFactory,
        InputFactory $iFactory
    )
    {
        $this->bFactory = $bFactory;
        $this->app = $app;
        $this->lFactory = $lFactory;
        $this->analytic = $analytic;
        $this->iFactory = $iFactory;
        $this->productSelect = $productSelect;
        $this->requestMng = $requestMng;
        $this->alert = $alert;
    }

    public function create()
    {
        [, , $width,] = $this->app->getWindowSizes();
        $this->leftOffset = $width - $this->offset - $this->buttonWidth;
        $this->createProductSelect();
        $this->createInputNumber();
        $this->createAddProductButton();
        $this->createSubmitButton();
        $this->analytic->createStat($this);
//        $this->createStatLayer();
    }

    protected function createProductSelect()
    {
        [$left, $top] = $this->updateCreateLine($this->buttonHeight, $this->buttonWidth);
        $this->lFactory::createByLeftTop('Продукт:', $left, $top);
        $this->productSelect->create($left, $top + $this->offset);
    }

    protected function createInputNumber()
    {
        [$left, $top] = $this->updateCreateLine($this->buttonHeight, $this->buttonWidth);
        $this->lFactory::createbyLeftTop('Номер:', $left, $top);
        $this->inputText = $this->iFactory::createTextInput($left, $top + $this->offset);
    }


//    protected function createStatLayer()
//    {
//        $width = $this->buttonWidth;
//        $height = $this->buttonHeight;
//        [$left, $top] = $this->updateCreateLine($height, $width);
//        $this->makeStatLayer($height, $width, $left, $top);
//    }

//    protected function makeStatLayer(int $height, int $width, int $left, int $top)
//    {
//        $label = $this->lFactory::createBlank($left - 3 * $this->buttonHeight, $top);
//        $this->analytic->attachOutput($label);
//    }
    public function setStatAnalytic(GuiStat $analytic)
    {
        $this->analytic = $analytic;
    }

    public function createStatLabel(): LabelWrapper
    {
        [$left, $top] = $this->updateCreateLine($this->buttonHeight, $this->buttonWidth);
        return $this->lFactory::createBlank($left - 3 * $this->buttonHeight, $top);
    }

    protected function createSubmitButton()
    {
        $this->createButton('ЗАПИСАТЬ', \Closure::fromCallable([$this->requestMng, 'moveProduct']));
    }

    protected function createAddProductButton()
    {
        $this->createButton('+   ДОБАВИТЬ  ', \Closure::fromCallable([$this, 'addProductByInput']));
    }

    protected function addProductByInput()
    {
        $input = $this->inputText->getValue();
        $this->inputText->setValue('');
        $this->requestMng->addProduct($input);
    }

    protected function createButton(string $text, \Closure $on)
    {
        $callFactoryMethod = function ($text, $left, $top, $height, $width) use ($on) {
            $this->bFactory::createWithOn($text, $left, $top, $height, $width, $on);
        };
        $this->createElement($text, $this->buttonHeight, $this->buttonWidth, $callFactoryMethod);
    }

    protected function createElement(string $text, int $elHeight, int $elWidth, \Closure $createMethod)
    {
        [$left, $top] = $this->updateCreateLine($elHeight, $elWidth);
        $createMethod($text, $left, $top, $elHeight, $elWidth);
    }

    protected function updateCreateLine(int $elHeight, int $elWidth): array
    {
        $top = $this->topOffset;
        $this->topOffset += $this->offset + $elHeight;
        return [$this->leftOffset, $top];
    }

    protected function getCurrentLeftTopOffsets(): array
    {
        return [$this->leftOffset, $this->topOffset];
    }

    public function createNumberCounterLayer(): array
    {
        [$left, $top] = $this->getCurrentLeftTopOffsets();
        $this->lFactory::createByHeight($this->title, $left, $top, $this->titleHeight);
        $top += $this->titleHeight;
        $input = $this->iFactory::createByWidth($left, $top, $this->widthInputLabel);
        $productNumberLabel = $this->lFactory::createBlank($left + $this->margin + $this->widthInputLabel, $top);
        return [$input, $productNumberLabel];
    }


}