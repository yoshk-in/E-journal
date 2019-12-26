<?php


namespace App\GUI\handlers;


use App\base\AppMsg;
use App\domain\CasualProcedure;
use App\domain\PartialProcedure;
use App\domain\Product;
use App\GUI\ProductStateColorize;
use App\GUI\tableStructure\CellRow;
use App\GUI\scheduler\Scheduler;

class CellActivator
{

    private $scheduler;
    private $block;
    private $colorant;
    private $successFullMsg = 'Блок N%s процедура %s завершена';
    private $proc;
    private $row;

    public function __construct(Scheduler $scheduler, ProductStateColorize $colorant, Block $blocker)
    {
        $this->scheduler = $scheduler;
        $this->block = $blocker;
        $this->colorant = $colorant;
    }

    public function byProduct(CellRow $row, Product $product)
    {
        $this->proc = $proc = $product->getActiveProc();
        $this->row = $row;
        $this->activateCell();
        !($proc instanceof PartialProcedure && $proc->isStarted()) ?: $this->blockRowAndUpdateCellTask();
    }

    private function activateCell()
    {
        $this->row->activateCellById($this->proc->getIdState(), ($this->colorant)($this->proc));
    }

    private function blockRowAndUpdateCellTask()
    {
        $this->block->row($this->row);
        $this->scheduler->addTask(
            $this->proc->beforeEnd(),
            \Closure::fromCallable([$this, 'updateProcInfo']),
            $this->successFullProcAlert()
        );
    }

    private function updateProcInfo()
    {
        $this->proc->notify(AppMsg::CURRENT_PROCEDURE_INFO);
    }

    private function successFullProcAlert()
    {
        return sprintf($this->successFullMsg, $this->proc->getProduct()->getNumber(), $this->proc->getName());
    }

}