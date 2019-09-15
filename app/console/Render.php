<?php


namespace App\console;


use App\domain\AbstractProcedure;
use App\domain\Informer;
use App\domain\ISubscriber;
use App\domain\Product;

class Render implements ISubscriber
{
    const TITLES = [
        'информация по несданным блокам:',
        'отмечены следующие события:',
    ];

    const TIME_FORMAT = 'd-m-Y H:i';
    const NAME = 0;
    const NUMBER = 1;

    protected $products = [];
    protected $procedures = [];
    private $title = '';


    public function flush()
    {
        $flush = $this->products[0][self::NAME];
        foreach ($this->procedures as $key => $procedure) {
            $flush .= $this->string("Блок номер {$this->products[$key][self::NUMBER]}  {$this->string($procedure)}");
        }
        echo $flush;
    }

    public function notify($object)
    {
        switch (get_class($object)) {
            case Product::class:
                $this->products[] = $object->getNameAndNumber();
                $this->title = self::TITLES[0];
                $this->renderProductInfo($object->getInfo());
                break;
            case AbstractProcedure::class:
                $this->products[] = $object->getProduct()->getNameAndNumber();
                $this->title = self::TITLES[1];
                $this->renderProcedure(...$object->getInfo());
        }

    }

    protected function renderProductInfo(array $procedures)
    {
        foreach ($procedures as $procedure) {
            $this->renderProcedure(...$procedure);
        }
    }

    protected function renderProcedure(string $name, \DateTimeInterface $start = null, ?\DateTimeInterface $end = null, ?bool $finished = null, ?array $inners = null)
    {
        if (is_null($start)) return;
        $finish = $finished ? 'завершена' : 'завершится';
        $finish_string = $end ? ", $finish " . $this->timeToStr($end) : '';
        $block_info = "$name - процедура начата {$this->timeToStr($start)} $finish_string";
        if (!is_null($inners)) {
            foreach ($inners as $inner) {
                $block_info .= $this->renderProcedure(...$inner);
            }
        }
        $this->procedures[] = $block_info;
    }

    private function string(?string $string = null)
    {
        return $string . "\n";
    }

    private function timeToStr(\DateTimeInterface $time)
    {
        return $time->format(self::TIME_FORMAT);
    }
}