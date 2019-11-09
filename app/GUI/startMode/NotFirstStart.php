<?php


namespace App\GUI\startMode;


use App\GUI\ButtonFactory;
use App\GUI\GUIManager;
use App\GUI\MouseMng;
use App\GUI\NewClickHandler;
use App\GUI\ProductTableComposer;
use App\GUI\Response;
use App\GUI\TableFactory;
use Gui\Application;

class NotFirstStart extends StartMode
{
    private $tComposer;
    private $mouseMng;

    public function __construct(GUIManager $app, ProductTableComposer $tComposer, MouseMng $mouseMng)
    {
        parent::__construct($app);
        $this->tComposer = $tComposer;
        $this->mouseMng = $mouseMng;
    }

    function run(Response $response, Application $gui)
    {
        $table = new TableFactory(
            20, $startLeft = 20, 50, 100, $wide_cell = 600, $this->mouseMng
        );
        $this->tComposer->tableByResponse($table, $this->app->getProduct(), $response);
        $response->reset();

        ButtonFactory::createWithOn(
            function () {
                $this->app->update();
            },
            $table->getWidth() + $startLeft);
        $this->mouseMng->changeHandler(new NewClickHandler($this->app->getRequest(), $this->app));
    }
}