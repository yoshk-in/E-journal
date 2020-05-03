<?php


namespace App\GUI\components;


use App\domain\procedures\ProductMap;
use App\GUI\components\traits\IRerenderable;
use App\GUI\components\wrappers\WrapWrongConstructorVisualObject;
use App\GUI\requestHandling\RequestManager;
use App\GUI\grid\ReactCell;
use App\GUI\grid\ReactSpace;
use App\GUI\handlers\ProductCounter;
use App\GUI\helpers\TProductName;
use Gui\Components\InputNumber;
use Gui\Components\Label;
use Gui\Components\VisualObjectInterface;
use function App\GUI\size;

class Rerender implements IRerenderable
{
    use TProductName;

    protected ReactCell $statLabel;
    protected array $rerender = [];
    protected array $visualProductCounter = [];
    private RequestManager $requestMng;
    private ProductCounter $productCounter;


    public function __construct(RequestManager $requestManager, ProductCounter $productCounter)
    {
        $this->requestMng = $requestManager;
        $this->productCounter = $productCounter;
        $this->productCounter->setOutput($this);
    }

    public function getStatComponent(): ReactCell
    {
        if ($this->requestMng->isCountable()) {
            return $this->advancedStatLayer()->toDown($this->statLayer());
        } else {
            return $this->statLayer();
        }
    }

    public function updateStatBlock(): ?ReactCell
    {
        if (empty($this->visualProductCounter)) {
            $rerender = $this->getStatComponent();
            if ($rerender === $this->statLabel) return null;
            return $rerender;
        }
        return null;
    }

    protected function statLayer()
    {
        return empty($this->statLabel) ?
        $this->rerender['stat'] = $this->statLabel = new ReactCell(
            Label::class,
            size(200, 50),
        ) : $this->statLabel;
    }

    protected function advancedStatLayer()
    {
        $this->rerender['counterLabel'] = $this->visualProductCounter['label'] = new ReactCell(
            Label::class,
            size(150, 50),
        );
        $this->rerender['counterInput'] = $this->visualProductCounter['input'] = new ReactCell(
            InputNumber::class,
            size(50, 50),
            [],
            ['change' => function (VisualObjectInterface $input) {
                $this->productCounter->changeMonthlyCounter($input->getValue());
            }],
            WrapWrongConstructorVisualObject::class
        );
        return $this->visualProductCounter['label']
            ->toRight($this->visualProductCounter['space'] = (new ReactSpace(size(50, 50)))
                ->toRight($this->visualProductCounter['input'])
            );
    }

    public function rerender($key, $value)
    {
        if (!isset($this->rerender[$key])) return;
        $this->rerender[$key]->setValue($value);
    }
}