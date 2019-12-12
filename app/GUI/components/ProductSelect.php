<?php


namespace App\GUI\components;


use App\domain\ProductMap;
use App\events\Event;
use App\events\EventChannel;
use App\events\IObservable;
use App\events\TObservable;
use App\GUI\domainBridge\RequestManager;
use Gui\Components\Option;

class ProductSelect implements IObservable, Event
{
    use TObservable;

    private $map;
    private $requestMng;
    private $productMap;
    private $options = [];
    private $event = Event::GUI_PRODUCT_CHANGED;

    public function __construct(ProductMap $productMap, RequestManager $requestMng, EventChannel $channel)
    {
        $this::attachToEventChannel($channel);
        $this->requestMng = $requestMng;
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


    public function emitChangeProductEvent(string $product)
    {
        $newProduct = $this->options[$product];
        if ($newProduct === $this->requestMng->getProduct()) return;
        $this->requestMng->changeProduct($newProduct);
        $this->notify($this->event);
    }

}