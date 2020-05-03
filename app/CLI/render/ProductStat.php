<?php


namespace App\CLI\render;


use App\domain\AbstractProduct;

class ProductStat
{
    const TITLE = Format::STAT_TITLE . Format::EOL;
    const INFO = Format::STAT . Format::EOL;
    const DELIMITER = Format::COUNT_DELIMITER;
    private array  $statBuffer = [];
    private string $output = '';
    private int $productCount = 0;
    protected string $title = "текущая статистика:\n";


    public function getOutput(array $products): string
    {
        $this->makeStatForArray($products);
        $this->renderStatBuffer();
        return $this->title . $this->renderCountProducts() . $this->output;
    }


    public function getComputed(): string
    {
        $this->renderStatBuffer();
        return $this->renderCountProducts() . $this->output;
    }

    public function makeStatForArray(array $products)
    {
        foreach ($products as $product) {
            $this->computeOne($product);
        }
    }

    public function resetOutput()
    {
        $this->output = '';
    }

    public function resetBuffer()
    {
        $this->output = '';
        $this->statBuffer = [];
        $this->productCount = 0;
    }


    public function computeOne(AbstractProduct $product)
    {
        $this->statBuffer[$product->getProcessingInner()->getName()][] = $product->getProductNumber();
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