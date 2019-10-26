<?php


namespace App\GUI;




class NewClickHandler extends ClickHandler
{

    public static function handle(Shape $emitter, string $prevColor)
    {
        $emitter = $emitter->getOwner()->getActiveCell();
        $emitter->plusClickCounter();
        if ($emitter->getClickCounter() % 2 === 0) {
            $emitter->setBorderColor(Color::WHITE);
            GUIManager::getBuffer()->removeBlock($emitter->getOwner()->getData());
        } else {
            $emitter->setBorderColor(self::$nextColor[$emitter->getOwner()->getActiveColor()]);
            GUIManager::getBuffer()->addBlock($emitter->getOwner()->getData());
        }
    }

}