<?php


namespace App\GUI\grid\style;

/**
 * Class Aligning
 * @package App\GUI\grid\style
 * @property Style $top;
 * @property Style $bottom;
 * @property Style $right;
 * @property Style $left;
 */

class Grid
{
    protected string $top = 'top';
    protected string $bottom = 'bottom';
    protected string $right = 'right';
    protected string $left = 'left';


    public function __set($name, Style $value)
    {
        if ($this->checkProperty($name)) {
            //@TODO
            return false;
        }
        return null;
    }

    public function __get($name): ?string
    {
        if ($this->checkProperty($name)) return $this->$name;
        return null;
    }

    protected function checkProperty($name): bool
    {
        $valid = property_exists($this, $name);
        assert($valid , 'property undefined');
        return $valid ?? false;
    }



}