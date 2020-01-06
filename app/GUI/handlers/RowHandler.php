<?php


namespace App\GUI\handlers;


use App\base\AppMsg;
use App\domain\AbstractProcedure;
use App\domain\CasualProcedure;
use App\domain\PartialProcedure;
use App\domain\Product;
use App\GUI\ProductStateColorant;
use App\GUI\tableStructure\TableRow;
use App\GUI\scheduler\Scheduler;

class RowHandler
{

    private Scheduler $scheduler;
    private Block $block;
    private ProductStateColorant $colorant;
    private string $successFullMsg = 'Блок N%s процедура %s завершена';
    private AbstractProcedure $proc;
    private TableRow $row;

    public function __construct(Scheduler $scheduler, ProductStateColorant $colorant, Block $blocker)
    {
        $this->scheduler = $scheduler;
        $this->block = $blocker;
        $this->colorant = $colorant;
    }

    public function byProduct(TableRow $row, Product $product)
    {
        $this->proc = $proc = $product->getProcessingProc();
        $this->row = $row;
        $this->activateCell();
        if ($proc instanceof PartialProcedure) {
            $proc->isStarted() ? $this->blockRowAndAddUpdateTask($proc) : $this->successFullProcAlert($product, $proc);
        }
    }

    public function unblockRow(TableRow $row)
    {
        $this->block->unblock($row);
    }

    private function activateCell()
    {
        $this->row->activateCellById($this->proc->getOrderNumber(), ($this->colorant)($this->proc));
    }

    private function blockRowAndAddUpdateTask(PartialProcedure $part)
    {
        $this->block->row($this->row);
        $this->scheduler->addTask(
            $part->beforeEnd(),
            fn() => $this->proc->notify(AppMsg::CURRENT_PROCEDURE_INFO)
        );
    }


    private function successFullProcAlert(Product $prod, PartialProcedure $proc)
    {
        $this->scheduler->alert(
            sprintf($this->successFullMsg, $prod->getNumber() ?? $prod->getAdvancedNumber(), $proc->getName())
        );
    }

}