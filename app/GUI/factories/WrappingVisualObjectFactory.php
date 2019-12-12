<?php


namespace App\GUI\factories;


use App\GUI\components\WrapVisualObject;
use Gui\Components\Button;
use Gui\Components\InputNumber;
use Gui\Components\VisualObjectInterface;

class WrappingVisualObjectFactory extends GuiComponentFactory
{

    public static function create(string $class, array $offsets, array $sizes, array $additions, ?string $wrapper = null, ?array $traits = null): VisualObjectInterface
    {
        $props = array_merge($offsets, $sizes, $additions);
        switch (true) {
            case is_null($traits):
                $wrapper = $wrapper ?? WrapVisualObject::class;
                return new $wrapper($class, $props);
//            case is_null($wrapper):
//                return new $class($props);
        }
        $anon = "\$object = new class($class::class) extends $wrapper {";
        foreach ($traits as $trait) {
            $anon .= "use $trait;\n";
        }
        $anon .= 'public function __construct(string $class, array $defaultAttributes = [], ContainerObjectInterface $parent = null, $application = null)'
            .'{parent::__construct($class, $defaultAttributes, $parent, $application);}' .
        "};";

        eval($anon);
        return $object;
    }
}