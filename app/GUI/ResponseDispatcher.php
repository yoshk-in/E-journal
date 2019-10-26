<?php


namespace App\GUI;


class ResponseDispatcher
{

    private $response;

    public function __construct(Response $response)
    {

        $this->response = $response;
    }

    public function handle($reporter)
    {
        $this->response->addInfo($reporter);
    }

    public function flush()
    {

    }
}