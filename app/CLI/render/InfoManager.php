<?php


namespace App\CLI\render;


use App\base\AbstractRequest;
use App\events\ISubscriber;


class InfoManager implements ISubscriber
{
    private $renderCommandResolver;
    private $eventRender = [];


    public function __construct(RenderResolver $renderCommandResolver)
    {
        $this->renderCommandResolver = $renderCommandResolver;
    }

    public function update(Object $observable, string $event)
    {
        $concrete_render = $this->renderCommandResolver->getEventRender($event);
        $concrete_render->render($observable);
        $this->eventRender[] = $concrete_render;
    }

    public function flush(AbstractRequest $request)
    {
        foreach ($this->eventRender as $render) {
            $render->flush($request);
        }
    }


}