<?php


namespace App\GUI\components;


use App\domain\ProductMap;
use App\events\Event;
use App\events\IEvent;
use App\events\TObservable;
use Gui\Components\Option;

class ProductSelectEmitter implements IEvent
{
    use TObservable;

    private ProductMap $productMap;
    private array $options = [];
    private array $optionValues = [];
    private string $event = IEvent::GUI_PRODUCT_CHANGED;
    private string $selected;

    public function __construct(array $optionValues)
    {
        $this->optionValues = $optionValues;
    }

    public function getOptions(): array
    {
        $key = 0;
        foreach ($this->optionValues as $product => $props) {
            $options[] = new Option($product, $key);
            $this->options[$key++] = $product;
        }
        return $options ?? [];
    }

    public function getSelected()
    {
        return $this->selected;
    }


    public function emitChangeProductEvent(string $option)
    {
        $this->selected = $this->options[$option];
        $this->update(new Event($this->event, $this));
    }

}