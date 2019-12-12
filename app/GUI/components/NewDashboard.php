<?php


namespace App\GUI\components;


use App\domain\ProductMap;
use App\GUI\components\wrappers\WrapWrongConstructorVisualObject;
use App\GUI\Debug;
use App\GUI\domainBridge\RequestManager;
use App\GUI\grid\Grid;
use App\GUI\grid\ReactGrid;
use App\GUI\grid\ReactCell;
use App\GUI\grid\ReactSpace;
use Gui\Components\Button;
use Gui\Components\InputNumber;
use Gui\Components\Label;
use Gui\Components\Select;
use Gui\Components\VisualObjectInterface;
use function App\GUI\{color, offset, size, text, value};

class NewDashboard
{

    protected $productSelector;
    protected $grid;
    private $requestManager;
    private $productMap;
    private $productSelect;
    private $numberInput;
    private $addProductButton;
    private $submitButton;


    public function __construct(RequestManager $requestManager, ProductMap $productMap, ProductSelect $productChange)
    {
        $this->requestManager = $requestManager;
        $this->productMap = $productMap;
        $this->productSelect = $productChange;
    }

    public function create()
    {

        $this->grid = new ReactGrid(
            $label = $this->productSelector()->toDown(
                $this->productNumberInput()->toDown(
                    (new ReactSpace(size(0, 50)))->toDown(
                        $this->addProductButton()
                            ->toDown(
                                (new ReactSpace(size(0, 50)))->toDown(
                                        $this->submitButton()
                                    )
                            )
                    )
                )
            ),
            offset(1200, 50)
        );

        $this->productSelector->setOptions($this->productSelect->getOptions())->setReadOnly(true);
        $this->numberInput->setMax(130000)->setValue(120100);
        Debug::setTimeout(function () use ($label) {$label->setVisible(false);});


    }

    protected function productSelector()
    {
        $label = new ReactCell(
            Label::class,
            size(50, 70),
            text('Продукт:'),
        );

        $this->productSelector = new ReactCell(
            Select::class,
            size(200, 70),
            [],
            ['change' => function (VisualObjectInterface $selector) {
                $this->productSelect->emitChangeProductEvent($selector->getChecked());
            }],
        );

        return $label->toRight((new ReactSpace(size(10, 0)))->toRight($this->productSelector));
    }

    protected function productNumberInput()
    {
        $label = new ReactCell(
            Label::class,
            size(50, 50),
            text('Номер:'),
        );

        $this->numberInput = new ReactCell(
            InputNumber::class,
            size(200, 50),
            [],
            [],
            WrapWrongConstructorVisualObject::class
        );

        return $label->toRight((new ReactSpace(size(13, 0)))->toRight($this->numberInput));
    }

    protected function addProductButton()
    {
        $this->addProductButton = new ReactCell(
            Button::class,
            size(263, 50),
            value(' +  ДОБАВИТЬ НОМЕР'),
        );
        return $this->addProductButton;
    }

    protected function submitButton()
    {
        $this->submitButton = new ReactCell(
            Button::class,
            size(263, 50),
            value('ЗАПИСАТЬ ИЗМЕНЕНИЯ')
        );
        return $this->submitButton;
    }

    protected function statLayer()
    {

    }


    protected function grid(): Grid
    {
        return $this->grid;
    }
}