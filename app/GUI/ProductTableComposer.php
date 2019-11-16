<?php


namespace App\GUI;


use App\base\AppMsg;
use App\domain\CasualProcedure;
use App\domain\CompositeProcedure;
use App\domain\ProcedureMap;
use App\domain\Product;
use App\events\ISubscriber;
use App\events\ProductTableSync;
use App\GUI\components\Cell;
use App\GUI\components\Pager;
use App\GUI\domainBridge\RowStore;
use App\GUI\factories\TableFactory;

class ProductTableComposer implements ISubscriber
{

    private $map;
    private $colorant;
    private $tSync;
    protected $currentTable;
    private $tFactory;
    private $mouseMng;
    private $productsPerPage = 15;
    private $tables = [];
    private $tableSizes = [ 20,  60,  50,  100, 600]; // [ left, top, height, widthCell, wideCell ]
    private $pager;
    private $visibleTable;
    private $store;

    const EVENTS = [
        AppMsg::GUI_INFO,
    ];


    public function __construct(ProcedureMap $map, ProductTableSync $tSync, MouseHandlerMng $click, Pager $pager, RowStore $store, $tFactory = TableFactory::class)
    {
        $this->map = $map;
        $this->colorant = ProdProcColorant::class;
        $this->tSync = $tSync;
        $this->tSync->attachTableComposer($this);
        $this->tFactory = $tFactory;
        $this->mouseMng = $click;
        $this->pager = $pager;
        $this->store = $store;
    }

    public function prepareTable(string $productName)
    {
        $this->pager->addLabel();
        $this->createPagerButton();
        $this->visibleTable = $this->currentTable = $this->tables[] = $this->tFactory::create($this->mouseMng, ...$this->tableSizes);
        $this->createHeaderRow($this->currentTable, $productName);
    }

    protected function createProductRow(Product $product)
    {
        $row = $this->currentTable->newRow($product->getNumber(), $product);
        $this->createHeaderCell($product);
        $this->createProcedureRow($this->currentTable, $product);
        //activate cells and sync by proc state
        $this->tSync->activateRowByProduct($row, $product);
        $this->store->add($product->getId(), $row);
    }

    public function unsetRow(CellRow $row)
    {
        $table = $row->getOwner();
        $table->unsetRow($row->getData()->getNumber());
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
        $this->currentTable->addClickTextCell($product->getNumber(), $this->colorant::productColor($product));
    }

    protected function createPagerButton()
    {
        $this->pager->add(function ($tableNumber) {
            $onClickTable = $this->tables[$tableNumber];
            if ($onClickTable === $this->visibleTable) return;
            $this->visibleTable->setVisible(false);
            $this->visibleTable = $onClickTable;
            $this->visibleTable->setVisible(true);
        });
    }


    public function update(Object $observable, string $event)
    {
        if ($this->currentTable->rowCount() === $this->productsPerPage)
        {
            $prevTable = $this->currentTable;
            $this->createPagerButton();
            $this->currentTable = $this->tables[] = $this->tFactory::create($this->mouseMng, ...$this->tableSizes);
            if ($prevTable == $this->visibleTable)
            {
                $prevTable->setVisible(false);
                $this->visibleTable = $this->currentTable;
                $this->visibleTable->setVisible(true);
            }
        }
        $this->createProductRow($observable);
    }

    public function subscribeOn(): array
    {
        return self::EVENTS;
    }
}