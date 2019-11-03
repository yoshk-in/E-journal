<?php


namespace App\GUI;


use App\domain\AbstractProcedure;
use App\domain\CasualProcedure;
use App\domain\CompositeProcedure;
use App\domain\ProcedureMap;
use App\domain\Product;

class ProductTableComposer
{

    private $map;

    public function __construct(ProcedureMap $map)
    {
        $this->map = $map;
    }

    public function tableByResponse(TableFactory $table, string $productName, Response $response)
    {
        // header row
        $this->createHeaderRow($table, $productName);

        foreach ($response->getInfo() as $product) {
            $table->newRow($product->getNumber(), $product);
            //y header
            $table->addClickTextCell($product->getNumber(), State::COLOR[$product->getCurrentProc()->getState()]);
            $this->createProductRow($table, $product);
        }
    }



    protected function createHeaderRow(TableFactory $table, string $productName)
    {
        //xy header - first cell
        $table->addTextCell('номера');

        foreach ($this->map->getProdProcArr($productName) as $proc) {
            (isset($proc['inners'])) ? $table->addWideTextCell($proc['name']) : $table->addTextCell($proc['name']);
        }

    }

    protected function createProductRow(TableFactory $table, Product $product)
    {
        foreach ($product->getProcedures() as $key => $procedure) {
            switch (get_class($procedure)) {
                case CompositeProcedure::class:
                    $shape = $this->createCompositeCell($table, $procedure);
                    break;
                default :
                   $shape = $this->createCasualCell($table, $procedure);
            }
            $procedure->attach($shape);
        }

    }

    protected function createCompositeCell(TableFactory $table, CompositeProcedure $procedure): Shape
    {
        $parts = $procedure->getInners();
        $composite = $table->beginCompositeCell($color = State::COLOR[$state = $procedure->getState()], $parts->count());
        $this->createPartialCells($table, $procedure);
        $table->finishCompositeCell();
        !$this->isActiveCell($table, $state) ?: $table->setRowActiveCell($composite, $color);
        return $composite;
    }

    protected function isActiveCell(TableFactory $table, int $state) : bool
    {
        $res = ($state !== AbstractProcedure::STAGE['end'] && is_null($table->getRowActiveCell())) ? true : false;
        return $res;
    }

    protected function createPartialCells(TableFactory $table, CompositeProcedure $procedure)
    {
        foreach ($procedure->getInners() as $part) {
            $shape = $table->addClickTextCell($part->getName(), $color = State::COLOR[$partState = $part->getState()]);
            $procedure->attach($shape);
            !$this->isActiveCell($table, $partState) ?: $table->setRowActiveCell($shape, $color);
        }
    }

    protected function createCasualCell(TableFactory $table, CasualProcedure $procedure): Shape
    {
        $shape = $table->addClickCell($color = State::COLOR[$state = $procedure->getState()]);
        !$this->isActiveCell($table, $state) ?: $table->setRowActiveCell($shape, $color);
        return $shape;
    }

}