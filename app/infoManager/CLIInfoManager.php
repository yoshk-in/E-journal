<?php


namespace App\infoManager;


use App\base\AbstractRequest;
use App\base\exceptions\WrongInputException;
use App\CLI\render\infoConstructor\AbstractInfoConstructor;
use App\CLI\render\Format;
use App\CLI\render\ProductStat;
use App\events\Event;
use App\events\IEvent;
use App\events\EventChannel;
use App\events\ISubscriber;
use App\events\traits\TObservable;


class CLIInfoManager
{

    private InfoEventResolver $dispatchResolver;
    private array $events = [];
    private AbstractRequest $request;


    public function __construct(InfoEventResolver $dispatchResolver, AbstractRequest $request)
    {
        $this->dispatchResolver = $dispatchResolver;
        $this->request = $request;
    }

    public function handleInfo($renderingSubject, $eventClass)
    {
        $dispatcher = $this->dispatchResolver->getEventHandler($eventClass);
        $dispatcher->handle($renderingSubject);
        $this->events[$eventClass] = $dispatcher;
    }


    public function dispatch()
    {
        if (empty($this->events)) {
            $this->emptyMessage();
            return;
        }
        /** @var AbstractInfoConstructor $dispatcher */
        foreach ($this->events as $dispatcher) {
            echo $dispatcher->getOutput();
        }
        /** @var ProductStat $statisic */
        $statistic = $this->dispatchResolver->getPostEventHandler(get_class($dispatcher));
        if (!is_null($statistic)) {
            echo PHP_EOL .
                $statistic->getOutput($dispatcher->getObserverBuffer());
        }
    }

    public function emptyMessage(array $numbers = [])
    {
        echo 'не найдено информации' . (
        $numbers ?
            " по данным номерам:" . implode(Format::COUNT_DELIMITER, $numbers)
            : ' или блоков в работе нет' . PHP_EOL);
    }
}