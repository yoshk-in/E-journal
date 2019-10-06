<?php


namespace App\console\render;


use App\events\ISubscriber;


class Render implements ISubscriber
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

    public function flush()
    {
        foreach ($this->eventRender as $render) {
            $render->flush();
        }
    }


}