<?php


namespace App\CLI\render;


use Doctrine\Common\Collections\Collection;

class CollFormatter
{
    private IRender $collFormatter;
    private string $delimiter;
    private string $collTitle;

    public function __construct(string $collDelimiter, string $collTitle)
    {
        $this->delimiter = $collDelimiter;
        $this->collTitle = $collTitle;
    }

    /**
     * @var Collection $coll
     * @return string
     */
    public function handle($coll): string
    {
        $buffer = $coll->isEmpty() ? '' : $this->collTitle;
        foreach ($coll as $unit) {
            $buffer .= $this->collFormatter->handle($unit) . $this->delimiter;
        }
        return $buffer;
    }


    public function setForEachFormatter(IRender $collFormatter)
    {
        $this->collFormatter = $collFormatter;
        return $this;
    }
}