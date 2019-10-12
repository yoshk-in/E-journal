<?php


namespace App\CLI\render;


class ProductStat
{
    const TITLE = Format::STAT_TITLE . Format::EOL;
    const INFO = Format::STAT . Format::EOL;
    const DELIMITER = Format::COMMA;


    public function get(\ArrayAccess $products)
    {
        $output = sprintf(self::TITLE, $products->count());
        $stat = $this->makeStat($products);
        foreach ($stat as $proc_name => $numbers) {
            $output .= sprintf(self::INFO, $proc_name, count($numbers), implode(self::DELIMITER, $numbers));
        }
        return $output;
    }

    protected function makeStat(\ArrayAccess $products): array
    {
        foreach ($products as $product) {
            $stat[$product->getCurrentProc()->getName()][] = $product->getNumber();
        }
        return $stat ?? [];
    }
}