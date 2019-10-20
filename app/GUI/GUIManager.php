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
        $this->gui->on('start', function () use ($product) {

            $composite_width = 600;
            $click = new ClickTransmit();
            $click->setClickStrategy(new ClickStrategy());

            //xy header
            $shapeFact = new InLineShapeFactory(20, 20, 50, 100);
            $shape = $shapeFact->addInRow();
            $text = TextFactory::inMiddle($shape, 'номера');


            //x header
            foreach ($this->procedureMap->getProdProcArr($product) as $proc) {

                if (isset($proc['inners'])) {
                    $shape = $shapeFact->addWithWidth(Color::WHITE, $composite_width);
                    TextFactory::inMiddle($shape, $proc['name']);

                } else {
                    $shape = $shapeFact->addInRow();
                    TextFactory::inMiddle($shape, $proc['name']);

                }
            }

            $shapeFact->newLine();
            $response = $this->doRequest();

            foreach ($response->getInfo() as $product) {

                //y header

                $shape = $shapeFact->addInRow();
                $text = TextFactory::inMiddle($shape, $product->getNumber());
                $click->fromTo($text, $shape);

                foreach ($product->getProcedures() as $procedure) {

                    switch (get_class($procedure)) {
                        case CompositeProcedure::class:

                            $parts = $procedure->getInners();

                            $top = $shapeFact->getTop() + 10;
                            $left = $shapeFact->getOffset() + 20;
                            $height = $shapeFact->getRowHeight() - 20;
                            $width = $composite_width / $parts->count() - 10;

                            $shapeFact->addWithWidth(State::COLOR[$procedure->getState()], $composite_width);

                            $partFact = new InLineShapeFactory($top, $left, $height, $width);
                            foreach ($parts as $part) {
                                $shape = $partFact->addInRow();
                                $text = TextFactory::inMiddle($shape, $part->getName());
                                $click->fromTo($text, $shape);
                            }
                            break;

                        default :
                            $shapeFact->addInRow(State::COLOR[$procedure->getState()]);
                    }
                }
                $shapeFact->newLine();
            }

        });
        $this->gui->run();
    }


    private function doRequest()
    {
        $this->app->run();
        return $this->response;
    }

}