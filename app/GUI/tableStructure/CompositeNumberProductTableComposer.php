<?php


namespace App\GUI\tableStructure;


use App\domain\Product;
use App\GUI\Color;
use App\GUI\components\IText;
use function App\GUI\textAndColor;

class CompositeNumberProductTableComposer extends ProductTableComposer
{

    protected function createProductNumberCell(Product $product)
    {
        if (is_null($product->getNumber())) {
            $this->inputCoverCell();
        }
        parent::createProductNumberCell($product);
        $this->textInMiddleCell();
    }

    protected function createProcedureRow(Product $product)
    {
        parent::createProcedureRow($product);
        $this->table()->addCell($this->textCell, textAndColor($product->getAdvancedNumber(), $this->colorize::productColor($product)));
    }
}