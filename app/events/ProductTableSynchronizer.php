<?php


namespace App\events;


use App\base\AppMsg;
use App\domain\AbstractProcedure;
use App\domain\Product;
use App\GUI\Debug;
use App\GUI\handlers\Block;
use App\GUI\handlers\CellActivator;
use App\GUI\handlers\GuiComponentDestroyer;
use App\GUI\ProdProcColorant;
use App\GUI\RowCellFactory;
use App\GUI\TableFactory;

class ProductTableSynchronizer implements ISubscriber
{
    private $table;
    private $cellActivate;
    private $colorant;
    private $destroyer;

    const EVENTS = [
        AppMsg::ARRIVE,
        AppMsg::DISPATCH,
        AppMsg::CURRENT_PROC_INFO
    ];

    public function __construct(CellActivator $cellActivate, EventChannel $eventChannel, GuiComponentDestroyer $destroyer)
    {
        $this->cellActivate = $cellActivate;
        $eventChannel->subscribe($this);
        $this->colorant = ProdProcColorant::class;
        $this->destroyer = $destroyer;
    }

    public function attachTable(TableFactory $table)
    {
        $this->table = $table;
    }

    public function notify(AbstractProcedure $proc)
    {
        $product = $proc->getProduct();
        if ($product->isEndLastProc()) {
            $this->destroyer->destroy($this->table->getRow($product->getNumber())->getCellsAndLabels());
            $this->table->unsetRow($product->getNumber());
            return;
        }
        $row = $this->table->getRow($product->getNumber());
        $cell = $row->getCell($proc->getIdState());
        $cell->resetClickCounter();
        $cell->setBackgroundColor($color = $this->colorant::color($proc));

        $row->getHeadCell()->setBackgroundColor($color);

        if ($proc->isFinished()) {
            Block::unblock($row);
        }

        $this->activateRowByProduct($row, $proc->getProduct());
    }

    public function activateRowByProduct(RowCellFactory $row, Product $product)
    {
        $this->cellActivate->byProduct($row, $product);
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