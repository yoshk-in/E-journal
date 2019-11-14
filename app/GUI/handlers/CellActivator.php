<?php


namespace App\GUI\handlers;


use App\base\AppMsg;
use App\domain\AbstractProcedure;
use App\domain\CasualProcedure;
use App\domain\CompositeProcedure;
use App\domain\PartialProcedure;
use App\domain\Product;
use App\GUI\ProdProcColorant;
use App\GUI\CellRow;
use App\GUI\scheduler\Scheduler;

class CellActivator
{

    private $scheduler;
    private $block;
    private $colorant;
    private $successFullMsg = 'Блок N%s процедура %s завершена';

    public function __construct(Scheduler $scheduler)
    {
        $this->scheduler = $scheduler;
        $this->block = Block::class;
        $this->colorant = ProdProcColorant::class;
    }

    public function byProduct(CellRow $row, Product $product)
    {
        $active = $product->getFirstUnfinishedProc() ?? $product->getCurrentProc();

        switch (get_class($active)) {
            case CompositeProcedure::class:
                $this->activateCompositeCellByProc($row, $active);
                break;
            case CasualProcedure::class:
                $this->setActiveCell($row, $active);
        }
    }

    private function activateCompositeCellByProc(CellRow $row, AbstractProcedure $active)
    {
        if ($active->areInnersFinished() || $active->isNotStarted()) {
            $this->setActiveCell($row, $active);
        } else {
            $this->activatePartialCellByProc($row, $active->getFirstUnfinishedProc());
        }
    }

    private function setActiveCell(CellRow $row, AbstractProcedure $procedure)
    {
        $row->setActiveCellById($procedure->getIdState(), $this->colorant::color($procedure));
    }

    private function activatePartialCellByProc(CellRow $row, PartialProcedure $active)
    {
        $this->setActiveCell($row, $active);

        if (!$active->isStarted()) return;

        $this->block::rowAndActiveCell($row);
        $this->scheduler->addTask(
            $active->beforeEnd(),
            function () use ($active) {
                $active->notify(AppMsg::CURRENT_PROCEDURE_INFO);
                return sprintf($this->successFullMsg, $active->getProduct()->getNumber(), $active->getName());
            }
        );


    }

}