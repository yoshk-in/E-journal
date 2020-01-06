<?php


namespace App\GUI\factories;


use App\GUI\components\WrapVisualObject;
use App\GUI\grid\style\Style;
use Gui\Components\Button;
use Gui\Components\ContainerObjectInterface;
use Gui\Components\InputNumber;
use Gui\Components\VisualObjectInterface;

class WrappingVisualObjectFactory extends GuiComponentFactory
{

    public static function create(Style $style, ContainerObjectInterface $parent = null, $application = null): VisualObjectInterface
    {
        $wrapper = $style->wrapComponentClass;
        [$class, $constructor] = $wrapper ?
            [$wrapper, [$style, $parent, $application]]
            :
            [$style->guiComponentClass, [$style->getVisualProps(), $parent, $application]];

        if (empty($traits = $style->traits)) {
            $object = new $class($constructor);
        } else {
            $anon = "\$object = new class($constructor) extends $class {";
            foreach ($traits as $trait) {
                $anon .= "use $trait;";
            }
            $anon .= '}';

            eval($anon);

        }
        return $object;
    }
}