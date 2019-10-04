<?php


namespace App\console\render;


use App\domain\AbstractProcedure;
use App\domain\CompositeProcedure;
use App\domain\Procedure;
use App\domain\Product;
use Doctrine\Common\Collections\Collection;


class Formatter implements Format
{
    private $procPattern = FORMAT::FULL_INFO;
    private $productNamePattern = Format::PRODUCT_NAME . Format::EOL;
    private $productNumberPattern = Format::PRODUCT_NUMBER . Format::EOL;
    private $casualProcPattern = FORMAT::FULL_INFO . Format::EOL;
    private $compositeProcPattern = Format::FULL_INFO . Format::COMPOSITE_CONJUCTION;
    private $partialProcPattern = FORMAT::SHORT_INFO;
    private $currentFormatProcFunc0 = 'fullFormatProc';
    private $currentFormatProcFunc1 = 'shortFormatProc';

    public function fullFormatProc(AbstractProcedure $procedure, string $pattern = Format::FULL_INFO)
    {
        return sprintf(
            $pattern,
            $procedure->getName(),
            $procedure->getStart() ?? ' - ',
            $procedure->getEnd() ?? ' - '
        );
    }

    public function shortFormatProc(AbstractProcedure $proc, string $pattern = Format::SHORT_INFO)
    {
        return sprintf($this->partialProcPattern, $proc->getName(), $this->getName() ?? ' - ');
    }


    public function formatProducts(\ArrayAccess $products, int $depth = 0): string
    {
        $output = $this->productNameStr($products[0]);
        foreach ($products as $product) {
            $output .= $this->productNumberStr($product);
            foreach ($product->getProcCollection() as $procedure) {
                switch (get_class($procedure)) {
                    case Procedure::class:
                        $output .= $this->fullFormatProc($procedure, $this->casualProcPattern);
                        break;
                    case CompositeProcedure::class:
                        $output .= $this->fullFormatProc($procedure, $this->compositeProcPattern);
                        foreach ($procedure->getInners() as $proc) {
                            $output .= $this->shortFormatProc($proc);
                        }

                }
            }
        }
    }

    public function formatStat(Collection $products) : string
    {
        $products->count();
    }

    private function productNameStr(Product $product): string
    {
        return sprintf($this->productNamePattern, $product->getName());
    }

    private function productNumberStr(Product $product)
    {
        return sprintf($this->productNumberPattern, $product->getNumber());
    }

}