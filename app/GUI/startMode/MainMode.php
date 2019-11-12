<?php


namespace App\GUI\startMode;


use App\GUI\factories\ButtonFactory;
use App\GUI\factories\TableFactory;
use App\GUI\GUIManager;
use App\GUI\MouseMng;
use App\GUI\NewClickHandler;
use App\GUI\ProductTableComposer;


class MainMode extends StartMode
{
    private $tComposer;
    private $mouseMng;
    private $buttonFactory;
    private $tFactory;
    private $top = 500;
    private $space = 100;

    public function __construct(GUIManager $app, ProductTableComposer $tComposer, MouseMng $mouseMng)
    {
        parent::__construct($app);
        $this->tComposer = $tComposer;
        $this->mouseMng = $mouseMng;
        $this->buttonFactory = ButtonFactory::class;
        $this->tFactory = TableFactory::class;
    }

    function run()
    {
        $offset = 20;
        $table = $this->tFactory::create($offset, $this->mouseMng);
        $this->tComposer->prepareTable($table, $this->app->getProduct());
        $mainSectionWidth = $table->getWidth();
        $this->createSubmitButton($mainSectionWidth, $offset);
        $this->createAddProductButton($mainSectionWidth, $offset);
        $this->mouseMng->changeHandler(new NewClickHandler($this->app->getRequest(), $this->app));
    }



    private function createSubmitButton(int $mainSectionWidth, $offset)
    {
        $this->buttonFactory::createWithOn(
            function () {
                $this->app->update();
            },
            $mainSectionWidth + $offset,
            $this->top + $this->space,
            'ЗАПИСАТЬ'
        );
    }

    private function createAddProductButton($mainSectionWidth, $offset)
    {
        $this->buttonFactory::createWithOn(
            function () {
                $this->app->addProduct();
            },
            $mainSectionWidth + $offset,
            $this->top,
            '+   ДОБАВИТЬ'
        );
    }

}