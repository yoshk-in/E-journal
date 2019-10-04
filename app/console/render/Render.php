<?php


namespace App\console\render;


use App\events\ISubscriber;

class Render implements ISubscriber
{
    private $renderCommandResolver;
    private $eventRender;
    private $procRender;

    public function __construct(RenderResolver $renderCommandResolver)
    {
        $this->renderCommandResolver = $renderCommandResolver;

    }

    public function update(Object $observable, string $event)
    {
        $this->eventRender = $this->renderCommandResolver->getEventRender($event);
        $this->procRender = $this->renderCommandResolver->getProcRender($observable);
        $this->eventRender->render($observable, $this->procRender);
    }


}