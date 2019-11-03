<?php


namespace App\GUI;




use App\base\GUIRequest;

class NewClickHandler extends ClickHandler
{
    private $request;

    public function __construct(GUIRequest $request)
    {
        $this->request = $request;
    }

    public function handle(Shape $emitter, string $prevColor)
    {
        $emitter = $emitter->getOwner()->getActiveCell();
        $emitter->plusClickCounter();
        if ($emitter->getClickCounter() % 2 === 0) {
            $emitter->setBorderColor(Color::WHITE);
            $this->request->removeBlock($emitter->getOwner()->getData());
        } else {
            $emitter->setBorderColor(self::$nextColor[$emitter->getOwner()->getActiveColor()]);
            $this->request->addBlock($emitter->getOwner()->getData());
        }
    }

}