<?php


namespace App\GUI;


use App\base\AppMsg;
use App\domain\CasualProcedure;
use App\domain\CompositeProcedure;
use App\domain\ProcedureMap;
use App\domain\Product;
use App\events\ISubscriber;
use App\events\ProductTableSynchronizer;
use App\GUI\components\Cell;

class ProductTableComposer implements ISubscriber
{

    private $map;
    private $colorant;
    private $synchronizer;
    protected $table;
    const EVENTS = [
        AppMsg::GUI_INFO,
    ];

    public function __construct(ProcedureMap $map, ProductTableSynchronizer $synchronize)
    {
        $this->map = $map;
        $this->colorant = ProdProcColorant::class;
        $this->synchronizer = $synchronize;
    }

    public function prepareTable(Table $table, string $productName)
    {
        $this->synchronizer->attachTable($this->table = $table);
        $this->createHeaderRow($table, $productName);
    }

    protected function createProductRow(Product $product)
    {
        $row = $this->table->newRow($product->getNumber(), $product);
        $this->createHeaderCell($product);
        $this->createProcedureRow($this->table, $product);
        //activate cells and sync by proc state
        $this->synchronizer->activateRowByProduct($row, $product);
    }


    protected function createHeaderRow(Table $table, string $productName)
    {
        //xy header - first cell
        $table->addTextCell('номера');

        foreach ($this->map->getProdProcArr($productName) as $proc) {
            (isset($proc['inners'])) ? $table->addWideTextCell($proc['name']) : $table->addTextCell($proc['name']);
        }

    }

    protected function createProcedureRow(Table $table, Product $product)
    {
        foreach ($product->getProcedures() as $procedure) {
            switch (get_class($procedure)) {
                case CompositeProcedure::class:
                    $this->createCompositeCell($table, $procedure);
                    break;
                default :
                    $this->createCasualCell($table, $procedure);
            }
        }
    }

    protected function createCompositeCell(Table $table, CompositeProcedure $procedure): Cell
    {
        $parts = $procedure->getInners();
        $composite = $table->beginCompositeCell($this->colorant::color($procedure), $parts->count());
        $this->createPartialCells($table, $procedure->getInners());
        $table->finishCompositeCell();
        return $composite;
    }


    protected function createPartialCells(Table $table, \ArrayAccess $inners)
    {
        foreach ($inners as $part) {
            $table->addClickTextCell($part->getName(), $this->colorant::color($part));
        }
    }

    protected function createCasualCell(Table $table, CasualProcedure $procedure): Cell
    {
        $shape = $table->addClickCell($this->colorant::color($procedure));
        return $shape;
    }

    protected function createHeaderCell(Product $product)
    {
        $this->table->addClickTextCell($product->getNumber(), $this->colorant::productColor($product));
    }


    public function update(Object $observable, string $event)
    {
        $this->createProductRow($observable);
    }

    public function subscribeOn(): array
    {
        return self::EVENTS;
    }
}