<?php


namespace App\GUI\handlers;


use App\GUI\components\GuiComponentWrapper;
use Gui\Application;
use React\EventLoop\LoopInterface;

class GuiComponentDestroyer
{

    private $gui;
    private $lastCall;
    private $loop;

    public function __construct(Application $gui, LoopInterface $loop)
    {
        $this->gui = $gui;
        $this->loop = $loop;
    }

    public function destroy(array $guiComponents)
    {
        if (is_null($this->lastCall)) {
            $this->destroyArrayOfArray($guiComponents);
        } else {
            $this->loop->futureTick($this->destroyClosure($guiComponents));
        }
    }

    public function destroyArrayOfArray(array $array)
    {
        foreach ($array as $sub_array) {
            foreach ($sub_array as $component) {
                $this->destroyElement($component);
            }
        }
    }

    private function resetLastCallClosure(): \Closure
    {
        return function () {
            $this->lastCall = null;
        };
    }

    private function destroyClosure(array $components): \Closure
    {
        return function () use ($components) {
            $this->destroyArrayOfArray($components);
            $this->loop->futureTick($this->resetLastCallClosure());
        };
    }

    private function destroyElement(GuiComponentWrapper $object)
    {
        $this->gui->destroyObject($object->getComponent());
    }

}