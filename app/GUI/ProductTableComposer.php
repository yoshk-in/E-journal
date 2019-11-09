<?php


namespace App\GUI;


use App\domain\CasualProcedure;
use App\domain\CompositeProcedure;
use App\domain\PartialProcedure;
use App\domain\ProcedureMap;
use App\domain\Product;
use App\events\ProcCellSynchronize;
use App\GUI\handlers\CellActivator;
class ProductTableComposer
{

    private $map;
    private $cellActivate;
    private $colorant;
    private $synchronize;

    public function __construct(ProcedureMap $map, CellActivator $cellActivate, ProcCellSynchronize $synchronize)
    {
        $this->map = $map;
        $this->cellActivate = $cellActivate;
        $this->colorant = ProdProcColorant::class;
        $this->synchronize = $synchronize;
    }

    public function tableByResponse(TableFactory $table, string $productName, Response $response)
    {
        // header row
        $this->createHeaderRow($table, $productName);

        foreach ($response->getInfo() as $key => $product) {
            $table->newRow($product->getNumber(), $product);
            //y header
            $table->addClickTextCell($product->getNumber(), $this->colorant::productColor($product));
            //rest data table
            $this->createProductRow($table, $product);

            //activate cells and sync by proc state
            $row = $table->getCurrentRow();
            $this->cellActivate->byProduct($row, $product);
            $this->synchronize->attachRowCells($row);
        }
    }


    protected function createHeaderRow(TableFactory $table, string $productName)
    {
        //xy header - first cell
        $table->addTextShape('номера');

        foreach ($this->map->getProdProcArr($productName) as $proc) {
            (isset($proc['inners'])) ? $table->addWideTextShape($proc['name']) : $table->addTextShape($proc['name']);
        }

    }

    protected function createProductRow(TableFactory $table, Product $product)
    {
        foreach ($product->getProcedures() as $procedure) {
            switch (get_class($procedure)) {
                case CompositeProcedure::class:
                    $this->createCompositeCell($table, $procedure);
                    break;
                default :
                    $this->createCasualCell($table, $procedure);
            }
        }
    }

    protected function createCompositeCell(TableFactory $table, CompositeProcedure $procedure): Cell
    {
        $parts = $procedure->getInners();
        $composite = $table->beginCompositeCell($this->colorant::color($procedure), $parts->count());
        $this->createPartialCells($table, $procedure->getInners());
        $table->finishCompositeCell();
        return $composite;
    }


    protected function createPartialCells(TableFactory $table, \ArrayAccess $inners)
    {
        foreach ($inners as $part) {
            $table->addClickTextCell($part->getName(), $this->colorant::color($part));
        }
    }

    protected function createCasualCell(TableFactory $table, CasualProcedure $procedure): Cell
    {
        $shape = $table->addClickCell($this->colorant::color($procedure));
        return $shape;
    }


}