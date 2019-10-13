<?php


namespace App\GUI;


use App\base\AppMsg;
use App\base\GUIRequest;
use App\controller\Controller;
use Gui\Application;
use Gui\Components\Button;
use Gui\Components\InputText;
use Gui\Components\Label;
use Gui\Components\Shape;
use Gui\Components\Window;

class GUIManager
{
    private $gui;
    private $request;
    private $response;
    private $app;


    public function __construct(GUIRequest $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function run(Controller $app)
    {
        $this->app = $app;
        $this->request->addCmd(AppMsg::INFO);
        $this->request->setProduct('Г9');
        $this->app->run();
        $this->gui = new Application([
            'title' => 'ЭЛЕКТРОННЫЙ ЖУРНАЛ',
            'left' => 248,
            'top' => 50,
            'width' => 860,
            'height' => 600,
        ]);
        $this->gui->on('start', function () {
        $this->debug();

        });
        $this->gui->run();
    }

    private function debug()
    {
        $this->request->addCmd(AppMsg::INFO);
        $this->app->run();
        $text = $this->response->getInfo()->count();
        new Label([
            'text' => $text,
            'top' => 10,
            'fontSize' => 10,
            'left' => 10,
        ]);
    }

}