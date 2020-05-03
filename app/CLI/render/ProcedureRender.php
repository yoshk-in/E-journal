<?php


namespace App\CLI\render;


class ProcedureRender extends Render
{
    private ProcedureFormatter $procFormatter;
    private CollFormatter $collectionFormatter;


    final function handle($processed): string
    {
        return $this->procFormatter->handle($processed) . $this->collectionFormatter->handle($processed->getProcedures());
    }

    final protected function doHandle($processed): void
    {
    }

    public function setFormatters(ProcedureFormatter $casual, CollFormatter $collFormatter)
    {
        $this->procFormatter = $casual;
        $this->collectionFormatter = $collFormatter;
        return $this;
    }


}