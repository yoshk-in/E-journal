<?php


namespace App\GUI\handlers;


use App\GUI\components\WrapVisualObject;
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
            $this->loop->futureTick(\Closure::fromCallable(call_user_func([$this, 'throttledDestroy'], $guiComponents)));
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

    private function resetLastCallClosure()
    {
        $this->lastCall = null;
    }

    private function throttledDestroy(array $components)
    {
        $this->destroyArrayOfArray($components);
        $this->loop->futureTick(\Closure::fromCallable([$this, 'resetLastCallClosure']));
    }

    private function destroyElement(WrapVisualObject $object)
    {
        $this->gui->destroyObject($object->getComponent());
    }

}