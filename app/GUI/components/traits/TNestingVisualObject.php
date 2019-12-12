<?php


namespace App\GUI\components\traits;


use App\GUI\components\computers\SizeComputer;
use App\GUI\factories\GuiComponentFactory;
use App\GUI\factories\WrappingVisualObjectFactory;
use Gui\Components\VisualObjectInterface;

trait TNestingVisualObject
{
    protected $nested;
    protected $customFactory;
    protected static $nestingAligner;

    public function setCustomNestingObjectFactory(GuiComponentFactory $factory)
    {
        $this->customFactory = $factory;
    }

    public function nest(string $class, array $additions, ?array $onActions = null): self
    {
        $nestingAligner = self::$nestingAligner ?? function (array $offsets, array $sizes, array $additions) {
                return SizeComputer::inMiddle($offsets, $sizes, $additions);
            };
        [$offsets, $sizes, $additions] = ($nestingAligner)($this->getOffsets(), $this->getSizes(), $additions);
        $this->nested = $this->create($class, $offsets, $sizes, $additions);
        is_null($onActions) ?: $this->catchNestingActions($onActions);
        return $this;
    }


    protected function create(string $class,array $offsets, array $sizes, ?array $additions = null): VisualObjectInterface
    {
        $factory = $this->customFactory ?? WrappingVisualObjectFactory::class;
        return $factory::create($class, $offsets, $sizes, $additions);
    }

    public static function setNestingAligner(\Closure $closure)
    {
        self::$nestingAligner = $closure;
    }

    public function catchNestingActions(array $onActions)
    {
        foreach ($onActions as $action) {
            $this->nested->on($action, function () use ($action) { $this->fire($action); });
        }
    }

    public function setVisible($bool): self
    {
        $this->getComponent()->setVisible($bool);
        is_null($this->nested) ?: $this->nested->setVisible($bool);
        return $this;
    }
}