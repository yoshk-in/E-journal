<?php


namespace App\GUI;


use App\base\AppMsg;
use App\base\GUIRequest;
use App\controller\Controller;
use App\domain\ProcedureMap;
use Psr\Container\ContainerInterface;


class GUIManager
{
    private $gui;
    private $request;
    private $response;
    private $server;
    private $procedureMap;
    private static $deb;
    private $container;
    private $mouseMng;
    private $product;
    private $tComposer;
    private $table;


    public function __construct(ProcedureMap $procedureMap, ContainerInterface $container, MouseMnger $mouseMng, ProductTableComposer $tComposer)
    {
        $this->procedureMap = $procedureMap;
        $this->container    = $container;
        $this->request      = $container->get(GUIRequest::class);
        $this->response     = $container->get(Response::class);
        $this->mouseMng     = $mouseMng;
        $this->tComposer    = $tComposer;
    }

    public function run(Controller $app)
    {
        $this->server   = $app;
        $this->product  = $this->procedureMap->getProducts()[0];
        $this->gui      = self::$deb = WindowFactory::create();
        $response       = $this->firstRequest();

        $this->gui->on('start', function () use ($response) {
            $this->table = new TableFactory(
                20, 20, 50, 100, $wide_cell = 600, $this->mouseMng
            );
            $this->tComposer->tableByResponse($this->table, $this->product, $response);
            $this->response->reset();

            ButtonFactory::createWithOn(function () {
                $this->updateTable();
            });
            $this->mouseMng->changeHandler(new NewClickHandler($this->request));
        });
        $this->gui->run();
    }


    private function doRequest($cmd)
    {
        $this->request->prepareReq($cmd);
        $this->request->setProduct($this->product);
        $this->request->setPartial(null);
//        $this->alert($this->request);
        try {
            $this->server->run();
        } catch (\Exception $e) {
            $this->alert($e->getMessage());
        }
        return $this->response;
    }

    private function firstRequest(): Response
    {
        return $this->doRequest(AppMsg::INFO);
    }

    private function updateTable()
    {
        $this->doRequest(AppMsg::FORWARD);
        $this->response->reset();
    }

    public static function alert($text)
    {
        switch (gettype($text)) {
            case 'string':
                break;
            default:
//                ob_start();
//                xdebug_var_dump($text);
//                $text = ob_get_clean();
                $text = json_encode((array) $text, true);
//                if (json_last_error()) $text = json_last_error_msg();
//                $text = implode("\n", (array) $text);
//                $text = (array) $text;
//                array_walk_recursive($text, function (&$el) {
//                    if (is_array($el)) {
//                        implode("\n", (array) $el);
//                    }
//                });
//                $text = (array) $text;
//                $text = implode("\n",  $text);
        }

        (self::$deb)->alert($text);

    }




}