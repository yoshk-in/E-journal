<?php


namespace App\command\RepositoryCmd\foundHandler;


use App\domain\AbstractProduct;

class ChangeNumberHandler extends FoundHandler
{

    function handle($product, $request)
    {
        $oldNumber = $product->getProductId();
        $newNumber = $request->getProductNumbers()[$oldNumber];
        $product->changeNumbers($product->getPreNumber(), $newNumber);
    }

}