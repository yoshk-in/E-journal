<?php


namespace App\GUI\handlers;


use App\domain\procedures\CasualProcedure;
use App\events\Event;
use App\GUI\services\Alert;
use App\GUI\ProductStateColorant;
use App\GUI\tableStructure\TableRow;
use App\GUI\scheduler\Scheduler;

class RowHandler
{

    private Scheduler $scheduler;
    private Block $block;
    private ProductStateColorant $colorant;
    private string $successFullMsg = 'Блок N%s процедура %s завершена';
    private CasualProcedure $proc;
    private TableRow $row;
    private Alert $output;

    public function __construct(Scheduler $scheduler, ProductStateColorant $colorant, Block $blocker, Alert $output)
    {
        $this->scheduler = $scheduler;
        $this->block = $blocker;
        $this->colorant = $colorant;
        $this->output = $output;
    }

    public function byProduct(TableRow $row, CasualProcedure $proc)
    {
        $this->proc = $proc->getProductCurrentProc();
        $this->row = $row;
        $this->activateCell();
        if ($proc instanceof IAutoEndingProcedure) {
            $proc->isStarted() ? $this->blockRowAndAddUpdateTask($proc) : $this->successFullProcAlert($proc);
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

    private function blockRowAndAddUpdateTask(IAutoEndingProcedure $part)
    {
        $this->block->row($this->row);
        $this->scheduler->addTask(
            $part->beforeEnd(),
            fn() => Event::report($this->proc)
        );
    }


    private function successFullProcAlert(CasualProcedure $proc)
    {
        $this->output->send(
            sprintf($this->successFullMsg, $proc->getProductName() ?? $proc->getProductPreNumber(), $proc->getProductName())
        );
    }

}