<?php


namespace App\command\RepositoryCmd\foundHandler;


use App\domain\AbstractProduct;
use App\events\Event;

class ReportInfoHandler extends FoundHandler
{
    function handle($product, $request)
    {
        Event::report($product);
    }
}