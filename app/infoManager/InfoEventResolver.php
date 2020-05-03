<?php


namespace App\infoManager;

use App\CLI\render\infoConstructor\ProductInfoConstructor;
use App\CLI\render\infoConstructor\ProcedureInfoConstructor;
use App\CLI\render\ProductStat;
use App\domain\AbstractProduct;
use App\domain\procedures\CasualProcedure;
use App\events\IEvent;
use Psr\Container\ContainerInterface;


class InfoEventResolver
{
    private ContainerInterface $appContainer;

    const INFO_HANDLER = [
        CasualProcedure::class => ProcedureInfoConstructor::class,
        AbstractProduct::class => ProductInfoConstructor::class,
    ];

    const POST_INFO_HANDLER = [
        ProductInfoConstructor::class => ProductStat::class
    ];

    public function __construct(ContainerInterface $appContainer)
    {
        $this->appContainer = $appContainer;
    }

    public function getEventHandler($renderingClass)
    {
        return $this->appContainer->get(self::INFO_HANDLER[$renderingClass]);
    }

    public function getPostEventHandler(string $prevHandler): ?string
    {
        $next_handler = self::POST_INFO_HANDLER[$prevHandler] ?? null;
        return $next_handler ? $this->appContainer->get($next_handler) : null;
    }


}