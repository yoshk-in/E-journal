<?php


use App\infoManager\CLIInfoManager;
use App\domain\procedures\CasualProcedure;
use App\events\IEventType;
use function cfg\subscribe;

return [
    CLIInfoManager::class => subscribe([
        CasualProcedure::class => ['handleInfo', IEventType::ANY ],
    ])
];