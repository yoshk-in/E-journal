<?php


namespace App\GUI\components;


use App\domain\ProductMap;
use App\events\Event;
use App\events\EventChannel;
use App\events\IObservable;
use App\events\TObservable;
use App\GUI\requestHandling\RequestManager;
use Gui\Components\Option;

class ProductSelectEmitter implements IObservable, Event
{
    use TObservable;

    private ProductMap $productMap;
    private array $options = [];
    private string $event = Event::GUI_PRODUCT_CHANGED;

    public function __construct(ProductMap $productMap, EventChannel $channel)
    {
        $this::attachToEventChannel($channel);
        $this->productMap = $productMap;
    }

    public function getOptions(): array
    {
        $key = 0;
        foreach ($this->productMap->getProducts() as $product => $props) {
            $options[] = new Option($product, $key);
            $this->options[$key++] = $product;
        }
        return $options;
    }


    public function emitChangeProductEvent(string $option)
    {
        $newProduct = $this->options[$option];
        $this->notify($this->event, $newProduct);
    }

}