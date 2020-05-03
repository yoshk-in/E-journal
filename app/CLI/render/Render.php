<?php


namespace App\CLI\render;


use App\controller\TChainOfResponsibility;
use App\domain\procedures\traits\IProcedureOwner;
use App\domain\procedures\traits\IProductProcedure;

abstract class Render implements IRender, Format
{
    use TChainOfResponsibility;

    protected string $result = '';

    public function handle(IProductProcedure $processed): string
    {
        $next = $this->doHandle($processed);
        return $this->result . $this->next->handle($next);
    }


    abstract protected function doHandle($processed);
}