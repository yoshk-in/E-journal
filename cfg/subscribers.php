<?php



use App\domain\ProductMonthlyCounter;
use App\events\Event;
use function cfg\subscribe;
use App\domain\AbstractProduct;
use App\domain\data\AbstractProductData;
use App\controller\Controller;
use App\domain\procedures\proxy\UpdateProcedureProxy;
use App\domain\procedures\decorators\ProductOwnerDecorator;

return [
    ProductMonthlyCounter::class => subscribe([
        AbstractProduct::class => ['count', Event::START]
    ]),
    Controller::class => subscribe([
        AbstractProductData::class => ['productNameHasBeenSet', Event::PRODUCT_HAS_BEEN_SET],
        UpdateProcedureProxy::class => ['preDBSave'],
        ProductOwnerDecorator::class => ['productProcedureEnd', Event::END]
    ]),
];