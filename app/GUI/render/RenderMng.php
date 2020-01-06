<?php


namespace App\GUI\render;


use App\domain\ProductMap;
use App\events\EventChannel;
use App\GUI\components\Dashboard;
use App\GUI\components\Pager;
use App\GUI\requestHandling\RequestManager;
use App\GUI\tableStructure\DoubleNProductTableFormatter;
use App\GUI\tableStructure\ProductTableFormatter;
use App\GUI\tableStructure\ProductTableMng;
use App\helpers\AutoGenCollection;
use DI\Container;


class RenderMng
{

    private ?ProductTableMng        $currentTableMng = null;
    private RequestManager          $requestMng;
    private ProductMap              $productMap;
    private Dashboard               $dashboard;
    private AutoGenCollection       $tableComposersColl;


    public function __construct(RequestManager $requestMng,
                                ProductMap $productMap,
                                Container $container,
                                Dashboard $dashboard,
                                EventChannel $channel)
    {
        $this->requestMng = $requestMng;
        $this->productMap = $productMap;
        $this->dashboard = $dashboard;
        $this->tableComposersColl = new AutoGenCollection($container, $this->initTableMngCollectionProps($channel));
    }


    public function run()
    {
        $this->dashboard->create();
        $this->createOrChangeTableComposer();
    }

    public function createOrChangeTableComposer()
    {
        $product = $this->requestMng->getProduct();
        $dynProps = AutoGenCollection::getBlank();
        $dynProps->inject = [
            'formatter' =>
                $this->requestMng->isDoubleNumberProduct() ?
                    DoubleNProductTableFormatter::class
                    :
                    ProductTableFormatter::class
        ];

        $dynProps->scalar = ['product' => $product];
        $this->tableComposersColl->gen($product, $dynProps);
    }

    protected function initTableMngCollectionProps(EventChannel $channel)
    {
        $props = AutoGenCollection::getBlank();
        $props->class = ProductTableMng::class;
        $props->inject = ['pager' => Pager::class];

        $props->get = \Closure::fromCallable([$this, 'changeCurrentTable']);

        $props->make = function (ProductTableMng $newCurrent) use ($channel) {
            $this->changeCurrentTable($newCurrent);
            $channel->subscribe($newCurrent);
            $newCurrent->prepareTable();
            $this->requestMng->newProductRequest();
        };
        return $props;
    }

    protected function changeCurrentTable(ProductTableMng $newCurrent)
    {
        is_null($this->currentTableMng) ?: $this->currentTableMng->setVisible(false);
        ($this->currentTableMng = $newCurrent) && $newCurrent->setVisible(true);
    }


}


