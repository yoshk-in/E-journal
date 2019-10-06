<?php


namespace App\command;


use App\base\ConsoleRequest;
use App\console\render\Render;
use App\domain\ProcedureMapManager;
use App\repository\ProductRepository;

abstract class InfoCommand extends RepositoryCommand
{
    protected $render;

    public function __construct(
        ConsoleRequest $request,
        ProductRepository $repository,
        ProcedureMapManager $productMap,
        Render $render
    )  {
        parent::__construct($request, $repository, $productMap);
        $this->render = $render;
    }

    abstract protected function doExecute(
        $productName,
        $numbers,
        $procedure
    );
}