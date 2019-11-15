<?php


namespace App\events;


use App\base\AppMsg;
use App\domain\AbstractProcedure;
use App\domain\PartialProcedure;
use App\domain\Product;
use App\GUI\domainBridge\Store;
use App\GUI\handlers\Block;
use App\GUI\handlers\CellActivator;
use App\GUI\handlers\GuiComponentDestroyer;
use App\GUI\handlers\GuiStat;
use App\GUI\ProdProcColorant;
use App\GUI\CellRow;
use App\GUI\ProductTableComposer;

class ProductTableSync implements ISubscriber
{
    private $tComposer;
    private $cellActivate;
    private $colorant;
    private $store;
    private $destroyer;
    private $analytic;

    const EVENTS = [
        AppMsg::ARRIVE,
        AppMsg::DISPATCH,
        AppMsg::CURRENT_PROCEDURE_INFO
    ];

    public function __construct(CellActivator $cellActivate, GuiComponentDestroyer $destroyer, Store $store, GuiStat $analytic)
    {
        $this->cellActivate = $cellActivate;
        $this->colorant = ProdProcColorant::class;
        $this->destroyer = $destroyer;
        $this->store = $store;
        $this->analytic = $analytic;
    }

    public function attachTableComposer(ProductTableComposer $tComposer)
    {
        $this->tComposer = $tComposer;
    }

    public function notify(AbstractProcedure $proc)
    {
        $this->analytic->updateStat();
        $product = $this->productByProc($proc);
        if ($product->isFinished()) {
            $this->destroyTableRow($product);
            return;
        }
        $row = $this->updateRowCellByProc($proc);
        $this->activateRowByProduct($row, $product);

    }

    public function activateRowByProduct(CellRow $row, Product $product)
    {
        $this->cellActivate->byProduct($row, $product);
    }

    private function destroyTableRow(Product $product)
    {
        $row = $this->store->pop($product->getId());
        $this->destroyer->destroy($row->getCellsAndLabels());
        $this->tComposer->unsetRow($row);
    }

    private function productByProc(AbstractProcedure $proc): Product
    {
        return $proc->getProduct();
    }

    private function updateRowCellByProc(AbstractProcedure $proc): CellRow
    {
        $product = $this->productByProc($proc);
        $row = $this->store->get($product->getId());
        $cell = $row->getCell($proc->getIdState());
        $cell->resetClickCounter();
        $cell->setBackgroundColor($color = $this->colorant::color($proc));

        if (!$proc instanceof PartialProcedure) $row->getHeadCell()->setBackgroundColor($color);

        if ($proc->isFinished()) {
            Block::unblock($row);
        }
        return $row;
    }

    public function update(Object $observable, string $event)
    {
        $this->notify($observable);
    }

    public function subscribeOn(): array
    {
        return self::EVENTS;
    }

}