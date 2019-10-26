<?php


namespace App\GUI;


use App\base\AppMsg;
use App\base\GUIRequest;
use App\controller\Controller;
use App\domain\CompositeProcedure;
use App\domain\ProcedureMap;
use Gui\Application;
use Gui\Components\Button;
use Gui\Components\Label;
use function DI\string;


class GUIManager
{
    private $gui;
    private $request;
    private $response;
    private $app;
    private $procedureMap;
    private static $deb;
    /**
     * @var RequestMng
     */
    private static $buffer;


    public function __construct(GUIRequest $request, Response $response, ProcedureMap $procedureMap)
    {
        $this->request = self::$buffer = $request;
        $this->response = $response;
        $this->procedureMap = $procedureMap;

    }

    public function run(Controller $app)
    {
        $this->app = $app;
        $product = 'Г9';

        $this->gui = self::$deb = new Application([
            'title' => 'ЖУРНАЛ УЧЕТА',
            'left' => 248,
            'top' => 50,
            'width' => 1024,
            'height' => 600,
        ]);

        $response = $this->doRequest(AppMsg::INFO);

        $this->gui->on('start', function () use ($product, $response) {

            $wide_cell = 600;

            //xy header
            $table = new TableFactory(20, 20, 50, 100, $wide_cell);
             $table->addTextCell('номера');

            //x header
            foreach ($this->procedureMap->getProdProcArr($product) as $proc) {
                if (isset($proc['inners'])) {
                   $table->addWideTextCell($proc['name']);
                } else {
                    $table->addTextCell($proc['name']);
                }
            }

            $table->newRow();

            foreach ($response->getInfo() as $product) {

                //y header
                $table->setDataOnRow($product);
                $table->addClickTextCell($product->getNumber(), State::COLOR[$product->getCurrentProc()->getState()]);

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
                                },
                                State::COLOR[$procedure->getState()]);

                            break;
                        default :
                            $table->addClickCell(State::COLOR[$procedure->getState()]);
                    }
                }
                $table->newRow();
            }

            $button = new Button([
                'value' => 'отправить',
                'top' => 300,
                'left' => 300,
                'width' => 400,
                'height' => 200
            ]);
            $app = $this;
            $button->on('mousedown', function () use ($app) {
                $app->doRequest(AppMsg::FORWARD);
            });


            MouseManger::changeHandler(NewClickHandler::class);
        });
        $this->gui->run();
    }


    private function doRequest($cmd)
    {
        $this->request->setCmd($cmd);
        $this->request->setProduct('Г9');
        $this->request->setPartial(null);
        $this->app->run();
        return $this->response;
    }

    public static function alert($text)
    {

        switch (gettype($text)) {
            case 'string':
                break;
            case 'array':
                $text = implode("\n", $text);
                break;
            default:
                $text = (string) $text;
        }
        (self::$deb)->alert($text);

    }

    /**
     * @return RequestMng
     */
    public static function getBuffer()
    {
        return self::$buffer;
    }

}