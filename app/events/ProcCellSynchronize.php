<?php


namespace App\events;


use App\base\AppMsg;
use App\domain\AbstractProcedure;
use App\GUI\Debug;
use App\GUI\handlers\Block;
use App\GUI\handlers\CellActivator;
use App\GUI\ProdProcColorant;
use App\GUI\RowCellFactory;

class ProcCellSynchronize implements ISubscriber
{
    private $rows = [];
    private $cellActivate;
    private $colorant;
    const EVENTS = [
        AppMsg::ARRIVE,
        AppMsg::DISPATCH,
        AppMsg::CURRENT_PROC_INFO
        ];

    public function __construct(CellActivator $cellActivate, EventChannel $eventChannel)
    {
        $this->cellActivate = $cellActivate;
        $eventChannel->subscribe($this);
        $this->colorant = ProdProcColorant::class;
    }

    public function attachRowCells(RowCellFactory $row)
    {
        $this->rows[$row->getData()->getNumber()] = $row;
    }

    public function notify(AbstractProcedure $proc)
    {
        $row = $this->rows[$proc->getProduct()->getNumber()];

        $cell = $row->getCell($proc->getIdState());
        $cell->resetClickCounter();
        $cell->setBackgroundColor($color = $this->colorant::color($proc));

        $row->getHeadCell()->setBackgroundColor($color);

        if ($proc->isFinished()) {
            Block::unblock($row);
        }

        $this->cellActivate->byProduct($row, $proc->getProduct());

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