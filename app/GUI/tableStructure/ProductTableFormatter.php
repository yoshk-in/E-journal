<?php


namespace App\GUI\tableStructure;


use App\domain\CasualProcedure;
use App\domain\DoubleNumberStrategy;
use App\domain\CompositeProcedure;
use App\domain\PartialProcedure;
use App\domain\ProcedureMap;
use App\domain\Product;
use App\domain\ProductMap;
use App\GUI\components\Cell;
use App\GUI\components\computers\SizeComputer;
use App\GUI\IVisualClass;
use App\GUI\MouseHandlerMng;
use App\GUI\ProductStateColorize;
use Gui\Components\InputText;
use Gui\Components\Label;
use Gui\Components\Shape;
use Gui\Components\VisualObjectInterface;
use function App\GUI\color;
use function App\GUI\height;
use function App\GUI\offset;
use function App\GUI\size;
use function App\GUI\text;
use function App\GUI\textAndColor;
use function App\GUI\width;

class ProductTableFormatter
{
    protected array $textCell = [
        IVisualClass::WRAP => Cell::class,
        IVisualClass::MAIN => Shape::class,
        IVisualClass::NEST => Label::class
    ];

    protected array $casualCell = [
        IVisualClass::WRAP => Cell::class,
        IVisualClass::MAIN => Shape::class,
    ];

    protected array $inputNumberCell = [
        IVisualClass::WRAP => Cell::class,
        IVisualClass::MAIN => Shape::class,
        IVisualClass::NEST => InputText::class
    ];

    protected ProductStateColorize $colorize;
    private int $compositeWidth;
    private array $cellSize;
    private string $onCellClickEvent = 'mousedown';
    private MouseHandlerMng $click;
    private Table $handledTable;
    private ProductMap $productMap;
    private ProcedureMap $procedureMap;
    private string $handledProduct;

    public function __construct(ProductStateColorize $colorize, MouseHandlerMng $click, ProductMap $productMap, ProcedureMap $procedureMap)
    {
        $this->colorize = $colorize;
        $this->cellSize = size(100, 50);
        $this->compositeWidth = 150;
        $this->click = $click;
        $this->productMap = $productMap;
        $this->procedureMap = $procedureMap;
    }

    protected function textInMiddleCell()
    {
        Cell::setNestingAligner(function (array $offsets, array $sizes, array $additions) {
            return SizeComputer::textInMiddle($offsets, $sizes, $additions);
        });
    }

    public function createHeaderRow(string $product, Table $table): CellRow
    {
        $this->textInMiddleCell();
        $this->handledTable = $table;
        $this->handledProduct = $product;
        //xy header - first cell
        $header = $this->table()->getRow();
        $this->table()->addCell($this->textCell, text('номера'));

        //rest headers
        foreach ($this->procedureMap->getProceduresFor($product) as $proc) {
            $cellWidth = isset($proc['inners']) ?
                $this->procedureMap->partialsCount($this->product(), $proc['name']) * $this->compositeWidth
                :
                width($this->cellSize);

            $this->addClickHandler($this->table()->addCell(
                $this->textCell,
                text($proc['name']),
                size($cellWidth, height($this->cellSize)))
            );
        }
        return $header;
    }

    public function createProductRow(Product $product, Table $table): CellRow
    {
        $this->handledTable = $table;
        $row = $table->newRow($product->getId(), $product);
        $this->createProductNumberCell($product);
        $this->createProcedureRow($product);
        return $row;
    }

    protected function table(): Table
    {
        return $this->handledTable;
    }

    protected function product(): string
    {
        return $this->handledProduct;
    }


    protected function createProductNumberCell(Product $product)
    {
        if ($text = $product->getNumber() ?? '') {
            $cell = $this->textCell;
            $addition = [];
        }
        else {
            $cell = $this->inputNumberCell;
            $addition['onNest'] = null;
        }
        $this->addClickHandler($created_cell = $this->table()->addCell(
            $cell,
            array_merge(textAndColor($text, $this->colorize->productColor($product)), $addition)
        ));
        ($cell !== $this->inputNumberCell) ?: $created_cell->getNested()
            ->on('change',
                fn() => $this->click->getHandler()->handleInputNumber($created_cell)
            );
    }


    protected function createProcedureRow(Product $product)
    {
        foreach ($product->getProcedures() as $procedure) {
            $this->addClickHandler(
                $procedure instanceof CompositeProcedure ?
                    $this->compositeCell($procedure)
                    :
                    $this->casualCell($procedure)
            );
        }
    }

    protected function casualCell(CasualProcedure $procedure): VisualObjectInterface
    {
        return $this->table()->addCell($this->casualCell, color(($this->colorize)($procedure)));
    }

    protected function compositeCell(CompositeProcedure $procedure): VisualObjectInterface
    {
        $parts = $procedure->getInners();
        $compSizes = size($this->compositeWidth * $parts->count(), height($this->cellSize));
        $partsOffset = offset(10, 10);
        $cell = $this->table()->beginCompositeCell($this->casualCell, ($this->colorize)($procedure), $partsOffset, $compSizes, $parts->count());

        //create inner cells
        array_map(fn($part) => $this->addClickHandler($this->partialCell($part)), $parts->toArray());

        $this->table()->finishCompositeCell();
        return $cell;
    }

    protected function partialCell(PartialProcedure $part): VisualObjectInterface
    {
        $cell = $this->table()->addCell($this->textCell, textAndColor($part->getName(), ($this->colorize)($part)));
        $cell->catchNestingActions([$this->onCellClickEvent]);
        return $cell;
    }

    protected function addClickHandler(VisualObjectInterface $cell)
    {
        $cell->on($this->onCellClickEvent, fn() => $this->click->getHandler()->handle($cell));
    }


}