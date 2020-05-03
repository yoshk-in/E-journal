<?php


namespace App\GUI\requestHandling;


use App\domain\procedures\CasualProcedure;
use App\events\TCasualSubscriber;
use App\GUI\components\Cell;
use App\GUI\handlers\RowHandler;
use App\GUI\handlers\GuiDestroyer;
use App\GUI\ProductStateColorant;
use App\GUI\tableStructure\TableRow;
use App\GUI\tableStructure\ProductTableMng;

class ProductTableSync
{
    use TCasualSubscriber;

    private ProductTableMng $tableMng;
    private RowHandler $rowHandler;
    private ProductStateColorant $colorant;
    private RowStore $store;
    private GuiDestroyer $destroyer;


    /** @method callable updateRowOrDelete */
    const UPDATE_ROW_OR_DELETE = 'updateRowOrDelete';
    const UPDATE_ROW = 'updateRow';

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

    public function updateRowOrDelete(CasualProcedure $proc)
    {
        if ($proc->productIsEnded()) {
            $this->destroyTableRow($proc);
            return;
        }
        $this->updateRow($proc);
    }

    public function updateRow(CasualProcedure $proc)
    {
        $row = $this->updateRowCellByProc($proc);
        $this->activateRowCell($row, $proc);
    }

    public function activateRowCell(TableRow $row, CasualProcedure $product)
    {
        $this->rowHandler->byProduct($row, $product);
    }

    private function destroyTableRow(CasualProcedure $procedure)
    {
        $row = $this->store->pop($procedure->getProductId());
        $this->destroyer->destroy($row->getCellsAndLabels());
        $this->tableMng->unsetRow($row);
    }



    private function updateRowCellByProc(CasualProcedure $proc): TableRow
    {
        /** @var  Cell $cell */
        $row = $this->store->get($proc->getProductId());
        $cell = $row->getCell($proc->getOrderNumber());
        $cell->resetClickCounter();
        $cell->setBackgroundColor($color = $this->colorant::color($proc));

        if (!$proc->isPartial()) $row->getHeadCell()->setBackgroundColor($color);

        if ($proc->isEnded()) {
            $this->rowHandler->unblockRow($row);
        }
        return $row;
    }





}