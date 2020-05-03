<?php

use App\events\IEvent;
use App\events\IGUIEvent;
use App\GUI\GUIController;
use App\GUI\handlers\Alert;
use App\GUI\requestHandling\ProductTableSync;
use function cfg\subscribe;

return [
    GUIController::class => subscribe([
        IEvent::GUI_PRODUCT_CHANGED => [GUIController::GUI_PRODUCT_CHANGED]
    ]),
    ProductTableSync::class => subscribe([
        IEvent::PROCEDURE_CHANGE_STATE => [ProductTableSync::UPDATE_ROW_OR_DELETE],
        IEvent::CURRENT_PROCEDURE_INFO => [ProductTableSync::UPDATE_ROW]
    ]),
    Alert::class => subscribe([
        IGUIEvent::ALERT => [Alert::ALERT]
    ])
];