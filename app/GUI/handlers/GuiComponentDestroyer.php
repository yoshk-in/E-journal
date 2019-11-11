<?php


namespace App\GUI\handlers;


use App\GUI\components\GuiComponentWrapper;
use Gui\Application;

class GuiComponentDestroyer
{

    private $gui;

    public function __construct(Application $gui)
    {
        $this->gui = $gui;
    }

    public function destroy(array $guiComponents)
    {
        foreach ($guiComponents as $components) {
            foreach ($components as $component) {
                $this->destroyElement($component);
            }
        }
    }

    private function destroyElement(GuiComponentWrapper $object)
    {
        $this->gui->destroyObject($object->getComponent());
    }

}