<?php


namespace App\GUI\components\assertion;


use App\GUI\Color;
use App\GUI\components\IOffset;
use App\GUI\components\ISize;
use function App\GUI\left;

class ComponentIndexAssertion
{
    public static function check(array $offsets, array $sizes, array $additions)
    {
        assert(isset($offsets[IOffset::LEFT]),' wrong indexes of offsets or sizes');
        assert(isset($offsets[IOffset::TOP]),' wrong indexes of offsets or sizes');
        assert(isset($sizes[ISize::WIDTH]),' wrong indexes of offsets or sizes');
        assert(isset($sizes[ISize::HEIGHT]),' wrong indexes of offsets or sizes');
        assert(isset($additions[Color::KEY]),' wrong indexes of offsets or sizes');
    }
}