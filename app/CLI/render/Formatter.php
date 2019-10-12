<?php


namespace App\CLI\render;


use App\controller\TChainOfResponsibility;

abstract class Formatter implements IFormatter
{
    use TChainOfResponsibility;

    protected $buffer = '';

    public function handle($processed): string
    {
        $next = $this->doHandle($processed);
        return $this->buffer . $this->next->handle($next);
    }

    protected function exception()
    {
        throw new \Exception(' wrong class');
    }

    abstract protected function doHandle($processed);
}