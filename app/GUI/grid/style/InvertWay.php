<?php


namespace App\GUI\grid\style;


class InvertWay
{
    protected string $left = 'right';
    protected string $top = 'bottom';
    protected string $bottom = 'top';
    protected string $right = 'left';

    protected static  ?self $init = null;

    private function __construct()
    {

    }

    public static function init(): self
    {
        if (self::$init) return self::$init;
        return self::$init = new self();
    }

    public function __get($name): string
    {
        if (property_exists($this, $name)) return $this->$name;
        throw new \Exception('undefined property');
    }
}