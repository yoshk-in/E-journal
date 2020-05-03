<?php


namespace App\GUI\grid\style;



use Closure;

/**
 * @property int $left
 * @property int top
 * @property int $width
 * @property int $height
 */
class BasicVisualStyle
{
    //required gui Component Props
    protected array $props = ['left' => 0, 'top' => 0, 'width' => 0, 'height' => 0];
    const ALLOWED_PROPS = [
        'left' => 'int',
        'top' => 'int',
        'right' => 'int',
        'bottom' => 'int',
    ];

    //additional style props
    public int $margin = 0;
    public int $padding = 0;

    public InvertWay $invert;

    const UNDEFINED_PROP = 'undefined property';
    //defer computed props
    protected array $deferComputingProps = [];

    public function __construct()
    {
        $this->invert = InvertWay::init();
    }

    public function copy(): self
    {
        return clone $this;
    }


    public function getVisualProps(): array
    {
        return array_slice($this->props, 0, 4);
    }

    public function getProps(): array
    {
        return $this->props;
    }


    public function __set($name, $value)
    {
        $set = $this->setAllowedVisualProperty($name, $value);
        assert($set, self::UNDEFINED_PROP);
        return $set;
    }


    public function __get($name)
    {
        $prop = $this->getVisualProperty($name);
       assert( $prop,self::UNDEFINED_PROP);
       return $prop;
    }

    public function increaseLeftTopOn(int $value): self
    {
        $this->left += $value;
        $this->top += $value;
        return $this;
    }

    public function defer(string $name, Closure $value): self
    {
        $this->deferComputingProps[$name] = $value;
        return $this;
    }

    public function byDefer(string $name, $value)
    {
        $args = func_get_args();
        $defer = $this->deferComputingProps[array_shift($args)] ?? null;
        assert($defer, self::UNDEFINED_PROP);
        return $defer($args);
    }

    public function setAllowedVisualProperty($name, $value): bool
    {
        if (key_exists(static::ALLOWED_PROPS, $name)) {
            // method is_valid = is_int or is_string etc..
            assert(($is_valid = static::ALLOWED_PROPS[$name])($name),' wrong type property');
            $this->props[$name] = $value;
            return true;
        }
        return false;
    }

    public function getBasicProps(): array
    {
        return array_slice($this->props, 0, 4);
    }


    public function getVisualProperty($name): ?int
    {
        return $this->props[$name] ?? null;
    }
}