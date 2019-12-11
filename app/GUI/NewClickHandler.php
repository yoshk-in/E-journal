<?php


namespace App\GUI;



use App\base\GUIRequest;
use App\GUI\components\Cell;
use App\GUI\handlers\Alert;

class NewClickHandler extends ClickHandler
{
    private $request;
    private $alert;

    public function __construct(GUIRequest $request, Alert $alert)
    {
        $this->request = $request;
        $this->alert = $alert;
    }

    public function handle(Cell $emitter)
    {
        $row = $emitter->getOwner();
        if ($row->isBlock()) {
            $this->alert->alert($row->getData()->getConcreteUnfinishedProc()->getName() . ' не завершена');
            return;
        }

        $emitter = $row->getActiveCell();
        $emitter->plusClickCounter();
        if ($emitter->getClickCounter() % 2 === 0) {
            $emitter->default();
            $this->request->removeBlock($emitter->getData());
        } else {
            $emitter->setBorderColor(ProductStateColorize::nextColor($row->getActiveColor()));
            $this->request->addBlock($emitter->getData());
        }
    }

}