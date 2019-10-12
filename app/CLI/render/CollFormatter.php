<?php


namespace App\CLI\render;


class CollFormatter
{
    private $collFormatter;
    private $delimiter;

    public function __construct(string $delimiter = PHP_EOL)
    {
        $this->delimiter = $delimiter;
    }

    public function handle($coll): string
    {
        class_implements($coll, \ArrayAccess::class) || $this->exception();
        $buffer = '';
        foreach ($coll as $unit) {
            $buffer .= $this->collFormatter->handle($unit) . $this->delimiter;
        }
        return $buffer;
    }


    public function setForEachFormatter(IFormatter $collFormatter)
    {
        $this->collFormatter = $collFormatter;
        return $this;
    }
}