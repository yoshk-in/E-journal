<?php


namespace App\GUI\factories;


use App\GUI\components\WrapVisualObject;
use App\GUI\components\IOffset;
use App\GUI\components\ISize;
use Gui\Components\VisualObjectInterface;

class InputFactoryWrapping extends WrappingVisualObjectFactory
{

//    public static function create($left = 400, $top = 200): InputNumber
//    {
//        return (new InputNumber())
//            ->setWidth(70)
//            ->setHeight(50)
//            ->setTop($top)
//            ->setLeft($left)
//            ->setMax(999);
//    }
//
//    public static function createByWidth($left = 400, $top = 200, $width = 50): InputNumber
//    {
//        return (new InputNumber())
//            ->setWidth(70)
//            ->setHeight(50)
//            ->setTop($top)
//            ->setLeft($left)
//            ->setWidth($width)
//            ->setMax(999);
//    }
//
//    public static function createTextInput($left = 400, $top = 200, $width = 100): InputText
//    {
//        return (new InputText())
//            ->setWidth(70)
//            ->setHeight(50)
//            ->setTop($top)
//            ->setLeft($left)
//            ->setWidth($width)
//            ->setMax(999);
//    }

    public static function create(string $class, array $offsets, array $sizes, ?array $additions = null, ?string $wrap = null): VisualObjectInterface
    {
        return (new ($wrap ?? WrapVisualObject::class)($class))
            ->setHeight(50)
            ->setTop($offsets[IOffset::TOP])
            ->setLeft($offsets[IOffset::LEFT])
            ->setWidth($sizes[ISize::WIDTH]);
    }
}