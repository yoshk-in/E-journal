<?php


namespace App\GUI\handlers;


use App\base\AppMsg;
use App\domain\CasualProcedure;
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

    public function __construct(Scheduler $scheduler)
    {
        $this->scheduler = $scheduler;
        $this->block = Block::class;
        $this->colorant = ProductStateColorize::class;
    }

    public function byProduct(CellRow $row, Product $product)
    {
//        $this->handledProc = $proc = $product->getFirstUnfinishedProc() ?? $product->getCurrentProc();
//        $this->handledRow = $row;
//        if (get_class($proc) === CompositeProcedure::class && (!$proc->innersFinished() || $proc->isStarted())) {
//            $this->byPartialProc();
//            return;
//        }
//        $this->activateCell();
        $this->proc = $proc = $product->getActiveProc();
        $this->row = $row;
        $this->proc instanceof CasualProcedure ? $this->activateCell() : $this->activatePartialCell();

    }

//    private function byCompositeProc()
//    {
//        if ($this->handledProc->areInnersFinished() || $this->handledProc->isNotStarted()) {
//            $this->activateCell();
//        } else {
//            $this->byPartialProc();
//        }
//    }

    private function activateCell()
    {
        $this->row->activateCellById($this->proc->getIdState(), $this->colorant::color($this->proc));
    }

    private function activatePartialCell()
    {
//        $this->handledProc = $this->handledProc->getFirstUnfinishedProc();
        $this->activateCell();

        if (!$this->proc->isStarted()) return;

        $this->block::rowAndActiveCell($this->row);
        $this->scheduler->addTaskWithAlert(
            $this->proc->beforeEnd(),
            [\Closure::fromCallable([$this, 'updateProcInfo'])],
            [\Closure::fromCallable([$this, 'successFullProcAlert'])]
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