<?php


namespace App\GUI\response;


use App\GUI\ResponseDispatcher;

class NotFoundResponseDispatcher extends ResponseDispatcher
{
    public function handle($reporter)
    {
        $this->response->notFound();
    }
}