<?php


namespace App\GUI;



use App\base\GUIRequest;

class NewClickHandler extends ClickHandler
{
    private $request;
    private $gui;

    public function __construct(GUIRequest $request, GUIManager $gui)
    {
        $this->request = $request;
        $this->gui = $gui;
    }

    public function handle(Cell $emitter, string $prevColor)
    {
        $row = $emitter->getOwner();
        if ($row->isBlock()) {
            $this->gui->alert($row->getData()->getConcreteUnfinishedProc()->getName() . ' не завершена');
            return;
        }

        $emitter = $row->getActiveCell();
        $emitter->plusClickCounter();
        if ($emitter->getClickCounter() % 2 === 0) {
            $emitter->defaultBorderColor();
            $this->request->removeBlock($emitter->getData());
        } else {
            $emitter->setBorderColor(ProdProcColorant::nextColor($emitter->getOwner()->getActiveColor()));
            $this->request->addBlock($emitter->getData());
        }
    }

}