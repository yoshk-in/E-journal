<?php


namespace App\GUI;



use App\base\GUIRequest;
use App\GUI\components\Cell;
use App\GUI\components\WrapVisualObject;
use App\GUI\requestHandling\RequestManager;
use App\GUI\requestHandling\RowStore;
use App\GUI\handlers\Alert;
use App\GUI\tableStructure\CellRow;
use Gui\Components\VisualObjectInterface;

class DefaultClickHandler extends ClickHandler
{
    private RequestManager $requestMng;
    private Alert $alert;
    private Cell $active;
    private CellRow $row;
    private RowStore $store;
    private ProductStateColorize $colorize;

    public function __construct(RequestManager $requestMng, Alert $alert, RowStore $store, ProductStateColorize $colorize)
    {
        $this->requestMng = $requestMng;
        $this->alert = $alert;
        $this->store = $store;
        $this->colorize = $colorize;
    }

    public function handle(Cell $emitter)
    {
        $this->row = $emitter->getOwner();
        if ($this->row->isBlock()) {
            $this->alert->alert($this->row->getData()->getConcreteUnfinishedProc()->getName() . ' не завершена');
            return;
        }

        $this->active = $this->row->getActiveCell();
        $this->active->plusClickCounter();
        $this->active->getClickCounter() % 2 === 0 ? $this->unselectCell($this->active) : $this->selectCell($this->active);
    }

    public function handleInputNumber(Cell $cell)
    {
        $this->requestMng->addChangedMainNumber($cell->getNested()->getValue(), $cell->getData());
    }

    public function selectCell(Cell $cell)
    {
        $cell->setBorderColor($this->colorize->nextColor($cell->getOwner()->getActiveColor()));
        $this->requestMng->addData($cell->getData());
        $this->store->addSelectedCell($cell);
    }

    public function unselectCell(Cell $cell)
    {
        $cell->default();
        $this->requestMng->unsetData($cell->getData());
        $this->store->removeSelectedCell($cell);
    }

    public function areSelectedCellsExists(): bool
    {
        return !empty($this->store->getSelectedCells());
    }

    public function removeSelectedCells()
    {
        foreach ($this->store->getSelectedCells() as $cell) {
            $cell->default();
            $this->store->removeSelectedCell($cell);
        }
    }

}