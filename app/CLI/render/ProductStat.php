<?php


namespace App\CLI\render;


use App\domain\Product;

class ProductStat
{
    const TITLE = Format::STAT_TITLE . Format::EOL;
    const INFO = Format::STAT . Format::EOL;
    const DELIMITER = Format::COMMA;
    private $statBuffer = [];
    private $output = '';
    private $productCount = 0;


    public function renderStat(array $products): string
    {
        $this->makeStatForArray($products);
        $this->renderStatBuffer();
        return $this->renderCountProducts() . $this->output;
    }


    public function getStat(): string
    {
        $this->renderStatBuffer();
        return $this->renderCountProducts() . $this->output;
    }

    public function makeStatForArray(array $products)
    {
        foreach ($products as $product) {
            $this->oneProductStatStep($product);
        }
    }

    public function resetBuffer()
    {
        $this->output = '';
        $this->statBuffer = [];
        $this->productCount = 0;
    }


    public function oneProductStatStep(Product $product)
    {
        $this->statBuffer[$product->getCurrentProc()->getName()][] = $product->getNumber();
        ++$this->productCount;
    }

    private function renderStatBuffer()
    {
        foreach ($this->statBuffer as $proc_name => $numbers) {
            $this->output .= sprintf(self::INFO, $proc_name, count($numbers), implode(self::DELIMITER, $numbers));
        }
    }

    private function renderCountProducts(): string
    {
        return sprintf(self::TITLE, $this->productCount);
    }
}