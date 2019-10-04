<?php


namespace App\console\render;


use App\domain\AbstractProcedure;
use Psr\Container\ContainerInterface;

class RenderResolver implements RenderProp, Mode
{
    private $appContainer;


    public function __construct(ContainerInterface $appContainer)
    {
        $this->appContainer = $appContainer;
    }

    public function getEventRender(string $render)
    {
        return $this->appContainer->get($render);
    }

    public function getProcRender(AbstractProcedure $procedure)
    {
        return $this->appContainer->get(Mode::ALIAS[get_class($procedure)]);
    }

}