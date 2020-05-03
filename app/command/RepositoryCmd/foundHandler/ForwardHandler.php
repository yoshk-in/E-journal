<?php


namespace App\command\RepositoryCmd\foundHandler;


use App\domain\AbstractProduct;

class ForwardHandler extends FoundHandler
{

    function handle($product, $request)
    {
        $product->force();
    }
}