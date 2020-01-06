<?php


namespace App\CLI\render;


use App\controller\TChainOfResponsibility;

abstract class Formatter implements IFormatter
{
    use TChainOfResponsibility;

    protected string $result = '';

    public function handle($processed): string
    {
        $next = $this->doHandle($processed);
        return $this->result . $this->next->handle($next);
    }

    protected function exception()
    {
        throw new \Exception(' wrong class');
    }

    abstract protected function doHandle($processed);
}