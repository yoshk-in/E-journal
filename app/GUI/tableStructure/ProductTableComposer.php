<?php


namespace App\GUI\tableStructure;


use App\base\AppMsg;
use App\domain\CompositeNumberStrategy;
use App\domain\CompositeProcedure;
use App\domain\PartialProcedure;
use App\domain\ProcedureMap;
use App\domain\Product;
use App\domain\ProductMap;
use App\events\ISubscriber;
use App\GUI\components\Cell;
use App\GUI\components\computers\SizeComputer;
use App\GUI\domainBridge\ProductTableSync;
use App\GUI\components\Pager;
use App\GUI\domainBridge\RowStore;
use App\GUI\IVisualClass;
use App\GUI\MouseHandlerMng;
use App\GUI\ProductStateColorize;
use Gui\Components\InputText;
use Gui\Components\Label;
use Gui\Components\Shape;
use Gui\Components\VisualObjectInterface;
use Psr\Container\ContainerInterface;
use function App\GUI\{color, height, offset, size, text, textAndColor, width};

class ProductTableComposer implements ISubscriber
{

    private $map;
    protected $colorize;
    private $tSync;
    protected $currentTable;
    private $container;
    private $productsPerPage;
    private $header;
    private $tables = [];
    private $tableOffsets = [];
    private $cellSize = [];
    private $compositeWidth ;
    private $pager;
    private $visibleTable;
    private $store;
    private $productMap;
    private $product;
    private $events = [];
    private $onCellClickEvent = 'mousedown';

    protected $textCell = [
        IVisualClass::WRAP => Cell::class,
        IVisualClass::MAIN => Shape::class,
        IVisualClass::NEST => Label::class
    ];

    protected $casualCell = [
        IVisualClass::WRAP => Cell::class,
        IVisualClass::MAIN => Shape::class,
    ];

    protected $compositeNumberingCell = [
        IVisualClass::WRAP => Cell::class,
        IVisualClass::MAIN => Shape::class,
        IVisualClass::NEST => InputText::class
    ];

    const EVENTS = [
        AppMsg::GUI_INFO,
    ];
    private $click;

    public function __construct(ProcedureMap $map,
                                ProductMap $productMap,
                                ProductTableSync $tSync,
                                Pager $pager,
                                string $product,
                                RowStore $store,
                                ContainerInterface $container,
                                ProductStateColorize $colorant,
                                MouseHandlerMng $click)
    {
        $this->map = $map;
        $this->colorize = $colorant;
        $this->tSync = $tSync;
        $this->tSync->attachTableComposer($this);
        $this->container = $container;
        $this->pager = $pager;
        $this->store = $store;
        $this->productMap = $productMap;
        $this->product = $product;
        $this->tableOffsets = offset(20, 60);
        $this->cellSize = size(100, 50);
        $this->compositeWidth = 150;
        $this->productsPerPage = 15;
        $this->click = $click;
        $this->textInMiddleCell();
    }

    public function prepareTable()
    {
        $this->createPager();
        $this->visibleTable = $this->addTable($this->containerMakeTable());
        $this->createHeaderRow();
    }

    public function unsetRow(CellRow $row)
    {
        $table = $row->getOwner();
        $table->unsetRow($row->getData()->getNumber());
    }

    public function setVisible(bool $bool)
    {
        $this->table()->setVisible($bool);
        $this->header->setVisible($bool);
        $this->pager->setVisible($bool);
    }

    protected function textInMiddleCell()
    {
        Cell::setNestingAligner(function (array $offsets, array $sizes, array $additions) {
            return SizeComputer::textInMiddle($offsets, $sizes, $additions);
        });
    }

    protected function inputCoverCell()
    {
        Cell::setNestingAligner(function (array $offsets, array $sizes, array $additions) {
            return SizeComputer::inMiddle($offsets, $sizes, $additions);
        });
    }

    protected function containerMakeTable(): Table
    {
        return $this->container->make(Table::class, [
            'sizes' => $this->cellSize,
            'offsets' => $this->tableOffsets
        ]);
    }

    protected function createPager()
    {
        $this->pager->addTitle();
        $this->addPagerButton();
    }

    protected function addTable(Table $table): Table
    {
        return $this->tables[] = $this->currentTable = $table;
    }

    protected function table(): Table
    {
        return $this->currentTable;
    }

    protected function header(): CellRow
    {
        return $this->header;
    }

    protected function createProductRow(Product $product)
    {
        $row = $this->currentTable->newRow($product->getId(), $product);
        $this->createProductNumberCell($product);
        $this->createProcedureRow($product);
        //activate cells and sync by proc state
        $this->tSync->activateRowCell($row, $product);
        $this->store->add($product->getId(), $row);
    }


    protected function createHeaderRow()
    {
        //xy header - first cell
        $this->header = $this->table()->getRow();
        $this->table()->addCell($this->textCell, text('номера'));
        array_map(\Closure::fromCallable([$this, 'createHeaders']), $this->map->proceduresForProduct($this->product));

        //advanced number header column
         if ($this->productMap->getNumberStrategy($this->product) == CompositeNumberStrategy::class) {
             $cell = $this->table()->addCell($this->casualCell, text('Предвар. номер'));
             $this->addClickStrategyToCell($cell);
         }
    }

    protected function createHeaders(array $proc)
    {
        !isset($proc['inners']) ?: $partWidthSum = $this->map->partialsCount($this->product, $proc['name']) * $this->compositeWidth;
        $cell = $this->table()->addCell($this->textCell, (text($proc['name'])), size($partWidthSum ?? width($this->cellSize), height($this->cellSize)));
        $this->addClickStrategyToCell($cell);
    }

    protected function createProductNumberCell(Product $product)
    {
        $classes = ($text = $product->getNumber() ?? '') ? $this->textCell : $this->compositeNumberingCell;
        $cell = $this->table()->addCell($classes, textAndColor($text, $this->colorize::productColor($product)));
        $this->addClickStrategyToCell($cell);
    }

    protected function createProcedureRow(Product $product)
    {
        foreach ($product->getProcedures() as $procedure) {
            get_class($procedure) === CompositeProcedure::class ?
                $cell = $this->createCompositeCell($procedure)
                :
                $cell = $this->table()->addCell($this->casualCell, color(($this->colorize)($procedure)));
        }
        $this->addClickStrategyToCell($cell);
    }

    protected function createCompositeCell(CompositeProcedure $procedure): VisualObjectInterface
    {
        $parts = $procedure->getInners();
        $compSizes = size($this->compositeWidth * $parts->count() , height($this->cellSize));
        $partsOffset = offset(10, 10);
        $cell = $this->table()->beginCompositeCell($this->casualCell, ($this->colorize)($procedure), $partsOffset, $compSizes, $parts->count());
        array_map(\Closure::fromCallable([$this, 'createPartialCells']), $parts->toArray());
        $this->table()->finishCompositeCell();
        return $cell;
    }

    protected function createPartialCells(PartialProcedure $part)
    {
        $cell =  $this->table()->addCell($this->textCell, textAndColor($part->getName(), ($this->colorize)($part)));
        $this->addClickStrategyToCell($cell);
        $cell->transmitNestedActions([$this->onCellClickEvent]);
    }

    protected function addClickStrategyToCell(VisualObjectInterface $cell)
    {
        $cell->on($this->onCellClickEvent, function () use ($cell) {$this->click->getHandler()->handle($cell); });
    }

    protected function addPagerButton(): self
    {
        $this->pager->add(function ($tableNumber) {
            $onClickTable = $this->tables[$tableNumber];
            if ($onClickTable === $this->visibleTable) return;
            $this->switchVisibleTableOn($onClickTable);
        });
        return $this;
    }

    protected function switchVisibleTableOn(Table $table): self
    {
        $this->visibleTable->setVisible(false) && ($this->visibleTable = $table) && $this->visibleTable->setVisible(true);
        return $this;
    }

    protected function updateCurrentProductTable(Product $product): self
    {
        $this->table()->rowCount() < $this->productsPerPage ?: $this->createNewTable();
        $this->createProductRow($product);
        return $this;
    }

    protected function createNewTable(): self
    {
        ($prevTable = $this->currentTable) && $this->addPagerButton();
        $this->addTable($this->containerMakeTable()) && ($prevTable !== $this->visibleTable ?: $this->switchVisibleTableOn($prevTable));
        return $this;
    }


    public function update($product, string $event)
    {
        $this->updateCurrentProductTable($product);
    }

    public function subscribeOn(): array
    {
        $this->events[] = self::EVENTS[0] . $this->product;
        return $this->events;
    }
}