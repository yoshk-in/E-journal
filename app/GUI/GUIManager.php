<?php


namespace App\GUI;


use App\base\AppMsg;
use App\base\GUIRequest;
use App\controller\Controller;
use App\domain\CompositeProcedure;
use App\domain\ProcedureMap;
use Gui\Application;


class GUIManager
{
    private $gui;
    private $request;
    private $response;
    private $app;
    private $procedureMap;



    public function __construct(GUIRequest $request, Response $response, ProcedureMap $procedureMap)
    {
        $this->request = $request;
        $this->response = $response;
        $this->procedureMap = $procedureMap;
    }

    public function run(Controller $app)
    {
        $this->app = $app;
        $product = 'Г9';
        $this->request->addCmd(AppMsg::INFO);
        $this->request->setProduct($product);
        $this->gui = new Application([
            'title' => 'ЖУРНАЛ УЧЕТА',
            'left' => 248,
            'top' => 50,
            'width' => 1024,
            'height' => 600,
        ]);

        $response = $this->doRequest();

        $this->gui->on('start', function () use ($product, $response) {

            $wide_cell = 600;

            //xy header
            $table = new Table(20, 20, 50, 100, $wide_cell);
             $table->addTextShape('номера');

            //x header
            foreach ($this->procedureMap->getProdProcArr($product) as $proc) {
                if (isset($proc['inners'])) {
                   $table->addWideTextShape($proc['name']);
                } else {
                    $table->addTextShape($proc['name']);
                }
            }

            $table->newLine();

            foreach ($response->getInfo() as $product) {

                //y header
                $table->addClickTextShape($product->getNumber());

                foreach ($product->getProcedures() as $procedure) {

                    switch (get_class($procedure)) {
                        case CompositeProcedure::class:
                            $table->addCompositeShape(
                                $parts = $procedure->getInners(),
                                $parts->count(),
                                function ($part) {
                                    return $part->getName();
                                },
                                function ($proc) {
                                    return State::COLOR[$proc->getState()];
                                });
                            break;
                        default :
                            $table->addClickShape(State::COLOR[$procedure->getState()]);

                    }
                }
                $table->newLine();
            }
            MouseManger::changeHandler(NewClickHandler::class);
        });
        $this->gui->run();
    }


    private function doRequest()
    {
        $this->app->run();
        return $this->response;
    }

}