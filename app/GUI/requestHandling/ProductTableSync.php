<?php


namespace App\GUI\requestHandling;


use App\base\AppMsg;
use App\domain\AbstractProcedure;
use App\domain\PartialProcedure;
use App\domain\Product;
use App\events\Event;
use App\events\TCasualSubscriber;
use App\GUI\handlers\RowHandler;
use App\GUI\handlers\GuiDestroyer;
use App\GUI\ProductStateColorant;
use App\GUI\tableStructure\TableRow;
use App\GUI\tableStructure\ProductTableMng;
use App\events\ISubscriber;

class ProductTableSync implements ISubscriber
{
    use TCasualSubscriber;

    private ProductTableMng $tableMng;
    private RowHandler $rowHandler;
    private ProductStateColorant $colorant;
    private RowStore $store;
    private GuiDestroyer $destroyer;

    const EVENTS = [
        Event::PROCEDURE_CHANGE_STATE,
        AppMsg::CURRENT_PROCEDURE_INFO
    ];

    public function __construct(RowHandler $handler, GuiDestroyer $destroyer, RowStore $store, ProductStateColorant $colorant)
    {
        $this->rowHandler = $handler;
        $this->colorant = $colorant;
        $this->destroyer = $destroyer;
        $this->store = $store;
    }

    public function attachTableComposer(ProductTableMng $tableMng)
    {
        $this->tableMng = $tableMng;
    }

    public function notify(AbstractProcedure $proc)
    {
        $product = $this->productByProc($proc);
        if ($product->isEnded()) {
            $this->destroyTableRow($product);
            return;
        }
        $row = $this->updateRowCellByProc($proc);
        $this->activateRowCell($row, $product);

    }

    public function activateRowCell(TableRow $row, Product $product)
    {
        $this->rowHandler->byProduct($row, $product);
    }

    private function destroyTableRow(Product $product)
    {
        $row = $this->store->pop($product->getId());
        $this->destroyer->destroy($row->getCellsAndLabels());
        $this->tableMng->unsetRow($row);
    }

    private function productByProc(AbstractProcedure $proc): Product
    {
        return $proc->getProduct();
    }

    private function updateRowCellByProc(AbstractProcedure $proc): TableRow
    {
        $product = $this->productByProc($proc);
        $row = $this->store->get($product->getId());
        $cell = $row->getCell($proc->getOrderNumber());
        $cell->resetClickCounter();
        $cell->setBackgroundColor($color = $this->colorant::color($proc));

        if (!$proc instanceof PartialProcedure) $row->getHeadCell()->setBackgroundColor($color);

        if ($proc->isEnded()) {
            $this->rowHandler->unblockRow($row);
        }
        return $row;
    }

    public function update($observable, string $event)
    {
        $this->notify($observable);
    }



}