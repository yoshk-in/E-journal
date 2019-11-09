<?php


namespace App\GUI;


use App\base\GUIRequest;

class ResponseDispatcher
{

    protected $response;
    protected $request;

    public function __construct(GUIRequest $request, Response $response)
    {
        $this->response = $response;
        $this->request = $request;
    }

    public function handle($reporter)
    {
        $this->response->addInfo($reporter);
    }

    public function flush()
    {
        $this->request->reset();
    }
}