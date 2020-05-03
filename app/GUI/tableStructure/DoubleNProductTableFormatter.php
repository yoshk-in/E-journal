<?php


namespace App\GUI\tableStructure;


use App\domain\procedures\Product;
use App\GUI\components\Cell;
use App\GUI\components\computer\StyleComputer;
use function App\GUI\text;
use function App\GUI\textAndColor;

class DoubleNProductTableFormatter extends ProductTableFormatter
{
    protected function createProductNumberCell(Product $product)
    {
        if (is_null($product->getNumber())) {
            $this->inputCoverCell();
        }
        parent::createProductNumberCell($product);
        $this->textInMiddleCell();
    }

    public function createHeaderRow(string $product, Table $table): TableRow
    {
        $row =  parent::createHeaderRow($product, $table);
        //advanced number header column
        $this->addClickHandler($this->table()->addCell($this->casualCell, text('Предвар. номер')));
        return $row;
    }


    protected function createProcedureRow(Product $product)
    {
        parent::createProcedureRow($product);
        $this->advancedColumn($product);
    }


    protected function inputCoverCell()
    {
        Cell::setNestingAligner(function (array $offsets, array $sizes, array $additions) {
            return StyleComputer::inMiddle($offsets, $sizes, $additions);
        });
    }

    protected function advancedColumn(Product $product)
    {
        $this->addClickHandler($this->table()->addCell(
            $this->textCell,
            textAndColor($product->getAdvancedNumber(), $this->colorize->productColor($product))
        ));
    }

}