<?php


namespace App\command\RepositoryCmd\foundHandler;


use App\domain\AbstractProduct;
use App\events\Event;

class ReportProcInfoHandler extends FoundHandler
{

    function handle($product, $request)
    {
        Event::report($product->getLastEnded());
    }
}