<?php


namespace App\GUI\factories;


use App\GUI\components\WrapVisualObject;
use App\GUI\components\IText;
use App\GUI\Color;
use Gui\Components\Label;

class LabelFactory
{

//    public static function create(string $text, string $fontSize, int $width, int $height, $top, $left): LabelWrapper
//    {
//        $labelWrapper = new LabelWrapper(
//            [
//                'text' => $text,
//                'fontColor' => self::$color,
//                'left' => $left,
//                'top' => $top,
////                'width' => $width,   // may be is it not need?
////                'height' => $height,    // may be is it not need?
//                'fontSize' => $fontSize,
////                'backgroundColor' => Color::GREEN
//            ]
//        );
//        return $labelWrapper /* ->setTop($top) */ ;
//
//    }

    public static function create(array $offsets, array $sizes, array $additions): WrapVisualObject
    {
        $props = array_merge($offsets, $sizes, $additions);
        isset($props[IText::FONT_SIZE]) ?: $props[IText::FONT_SIZE] = 10;
        isset($props[IText::FONT_COLOR]) ?: $props[IText::FONT_COLOR] = Color::WHITE;
        return new WrapVisualObject(Label::class, $props);
    }
//
//    public static function createByHeight($text, $left, $top, $height)
//    {
//        return new LabelWrapper(
//            [
//                'text' => $text,
//                'fontColor' => self::$color,
//                'left' => $left,
//                'top' => $top,
//                'height' => $height,
//            ]
//        );
//    }
//
//    public static function createBlank($left, $top): LabelWrapper
//    {
//        return new LabelWrapper(
//            [
//                'fontColor' => self::$color,
//                'left' => $left,
//                'top' => $top
//            ]
//        );
//    }
//
//    public static function createBlankByWidth($left, $top, $width): LabelWrapper
//    {
//        return new LabelWrapper(
//            [
//                'fontColor' => self::$color,
//                'left' => $left,
//                'top' => $top,
//                'width' => $width
//            ]
//        );
//    }
//
//    public static function createBlankLabel($left, $top, $width)
//    {
//        return new LabelWrapper(
//            [
//                'fontColor' => self::$color,
//                'left' => $left,
//                'top' => $top,
//                'width' => $width
//            ]
//        );
//    }
//
//    public static function createByWidth(string $text, int $width, $top, $left): LabelWrapper
//    {
//        return new LabelWrapper(
//            [
//                'text' => $text,
//                'fontColor' => self::$color,
//                'left' => $left,
//                'top' => $top,
//                'width' => $width,
//
//            ]
//        );
//    }
//
//    public static function createByLeftTop(string $text, int $left, int $top): LabelWrapper
//    {
//        return new LabelWrapper(
//            [
//                'text' => $text,
//                'left' => $left,
//                'top' => $top
//            ]
//        );
//    }
//
//    public static function createSelectLabel(string $text): LabelWrapper
//    {
//        $labelWrapper = new LabelWrapper(
//            [
//                'text' => $text,
//                'fontColor' => self::$color
//            ]
//        );
//        $labelWrapper->setVisible(false);
//        return $labelWrapper;
//    }

}