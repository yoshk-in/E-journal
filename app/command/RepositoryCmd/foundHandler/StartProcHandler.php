<?php


namespace App\command\RepositoryCmd\foundHandler;


use App\base\exceptions\ProcedureException;
use App\domain\AbstractProduct;
use App\domain\procedures\interfaces\NameStateInterface;

class StartProcHandler extends FoundHandler
{

    function handle(NameStateInterface $product, $request)
    {
        $this->exceptionLessHandle(fn () => $product->start($request->getPartial()), $product);
    }
}