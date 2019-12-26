<?php


namespace App\GUI\components;


use App\events\EventChannel;
use App\GUI\components\traits\IRerenderable;
use App\GUI\components\wrappers\WrapWrongConstructorVisualObject;
use App\GUI\requestHandling\RequestManager;
use App\GUI\grid\Grid;
use App\GUI\grid\ReactGrid;
use App\GUI\grid\ReactCell;
use App\GUI\grid\ReactSpace;
use App\GUI\handlers\GuiProductStat;
use App\GUI\handlers\ProductCounter;
use Gui\Components\Button;
use Gui\Components\InputNumber;
use Gui\Components\Label;
use Gui\Components\Select;
use Gui\Components\VisualObjectInterface;
use function App\GUI\{color, offset, size, text, value};

class Dashboard
{

    protected $productSelector;
    protected $grid;
    private $requestMng;
    private $productChanger;
    private $productNumberInput;
    private $addProductButton;
    private $submitButton;
    private GuiProductStat $productStatistic;
    private Rerender $rerender;
    private ProductCounter $productCounter;
    private EventChannel$channel;
    private array $productSpecifics = [];

    public function __construct(
                                RequestManager $requestManager,
                                ProductSelectEmitter $productChanger,
                                Rerender $rerender,
                                GuiProductStat $statistic,
                                ProductCounter $counter,
                                EventChannel $channel
                                )
    {
        $this->requestMng = $requestManager;
        $this->channel = $channel;
        $this->productChanger = $productChanger;
        $this->rerender = $rerender;
        $this->channel->subscribe($this->productStatistic = $statistic);
        $this->productCounter = $counter;
        $this->productSpecifics = [$this->productCounter, $this->productStatistic];
    }

    public function create()
    {
        $startCell = $this->productSelector()->toDown(
            $this->productNumberInput()->toDown(
                (new ReactSpace(size(0, 50)))->toDown(
                    $this->addProductButton()
                        ->toDown(
                            (new ReactSpace(size(0, 50)))->toDown(
                                $this->submitButton()->toDown(
                                    (new ReactSpace(size(0, 50)))->toDown(
                                        $this->rerender->getStatComponent()
                                    )
                                )
                            )
                        )
                )
            )
        );

        $this->grid = new ReactGrid($startCell, offset(1200, 50));
        $this->grid->on(ReactGrid::EVENT['render'], function () {
            $this->productStatistic->updateStat();
            $this->productCounter->updateOutput();
            $this->productSelector->setOptions($this->productChanger->getOptions())->setReadOnly(true);
            $this->productNumberInput->setMax(130000)->setValue(120100);
        });
        $this->grid->render();


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
                $this->productChanger->emitChangeProductEvent($selector->getChecked());
                $this->channel->subscribeArray($this->productSpecifics);
                if ($updateStatBlock = $this->rerender->updateStatBlock()) {
                    $this->submitButton->toDown($updateStatBlock);
                }
                $this->productStatistic->updateStat();
                $this->productCounter->updateOutput();
                $this->productNumberInput->setValue(120100);

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

        $this->productNumberInput = new ReactCell(
            InputNumber::class,
            size(200, 50),
            [],
            [],
            WrapWrongConstructorVisualObject::class
        );

        return $label->toRight((new ReactSpace(size(13, 0)))->toRight($this->productNumberInput));
    }

    protected function addProductButton()
    {
        $this->addProductButton = new ReactCell(
            Button::class,
            size(263, 50),
            value(' +  ДОБАВИТЬ НОМЕР'),
            ['mousedown' => function () {
                $this->requestMng->addProduct($this->productNumberInput->getValue());
            }]
        );
        return $this->addProductButton;
    }

    protected function submitButton()
    {
        $this->submitButton = new ReactCell(
            Button::class,
            size(263, 50),
            value('ЗАПИСАТЬ ИЗМЕНЕНИЯ'),
            ['mousedown' => fn () => $this->requestMng->moveProductOrPersist()]
        );
        return $this->submitButton;
    }


}