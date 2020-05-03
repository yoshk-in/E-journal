<?php


namespace App\command\RepositoryCmd\foundHandler;



use App\domain\AbstractProduct;
use App\domain\procedures\interfaces\NameStateInterface;

class EndProcHandler extends FoundHandler
{

    function handle(NameStateInterface $product, $request)
    {
        $this->exceptionLessHandle(fn () => $product->end($request->getPartial()), $product);
    }
}