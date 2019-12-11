<?php


namespace App\GUI\components;


use App\domain\ProductMap;
use App\GUI\domainBridge\RequestManager;
use App\GUI\factories\WrappingVisualObjectFactory;
use App\GUI\grid\Grid;
use App\GUI\grid\GridSpace;
use App\GUI\grid\GridCell;
use Gui\Components\Button;
use Gui\Components\Label;
use Gui\Components\Option;
use Gui\Components\Select;
use Gui\Components\VisualObjectInterface;
use function App\GUI\{offset, size, text};

class NewDashboard
{

    protected $productSelector;
    protected $guiFactory;
    protected $grid;
    private $requestManager;
    private $productMap;
    private $productChange;


    public function __construct(WrappingVisualObjectFactory $guiFactory, RequestManager $requestManager, ProductMap $productMap, ProductChangeEvent $productChange)
    {
        $this->guiFactory = $guiFactory;
        $this->requestManager = $requestManager;
        $this->productMap = $productMap;
        $this->productChange = $productChange;
    }

    public function create()
    {

        $this->grid = new Grid(
            $this->productSelector()
            ->toDown(new GridSpace(size(0,0))),
            offset(1200, 50)
        );

       $this->productSelectorOptions();

    }

    protected function productSelector()
    {
        $productLabel = new GridCell(
            Label::class,
            size(50, 100),
            ['text' => 'Продукт:'],
        );

        $productSelector = new GridCell(
            Select::class,
            size(200, 100),
            [],
            ['change' => function (VisualObjectInterface $selector) {
                $this->productChange->emitEvent($selector->getChecked());
            }],
        );

        return $productLabel->toRight((new GridSpace(size(10, 0)))->toRight($this->productSelector = $productSelector));
    }

    protected function productSelectorOptions()
    {
        $this->productSelector->getComponent()->setOptions($this->productChange->getOptions())->setReadOnly(true);
    }

    protected function grid(): Grid
    {
        return $this->grid;
    }
}