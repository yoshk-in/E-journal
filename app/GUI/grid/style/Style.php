<?php


namespace App\GUI\grid\style;


use App\GUI\components\WrapVisualObject;
use App\GUI\factories\WrappingVisualObjectFactory;
use App\GUI\grid\traits\THierarchy;
use App\helpers\store\StoreInterface;
use Closure;
use Gui\Components\VisualObjectInterface;

/**
 * @property array $on
 * @property array $traits
 * @property Style $parent
 * @property string $defaultBorderColor
 * @property string $backgroundColor
 * @property string $color
 * @property string $fontColor
 * @property int $fontSize
 * @property string $borderColor
 * @property $value
 */
class Style extends BasicVisualStyle
{
    //creatingProps
    public string $guiComponentClass;
    public Closure $createCall;
    public Closure $afterCreateCall;
    public string $wrapComponentClass;
    public array $on = [];
    public array $traits = [];
    protected ?Style $child = null;
    public ?Style $parent = null;

    public string $defaultBorderColor;
    public StoreInterface $store;

    //additional component props
    const ALLOWED_PROPS = [
        'backgroundColor' => 'string',
        'color' => 'string',
        'fontColor' => 'string',
        'fontSize' => 'int',
        'borderColor' => 'string',
        'value' => 'scalar',
        'left' => 'int',
        'top' => 'int',
        'right' => 'int',
        'bottom' => 'int',
    ];

    public VisualObjectInterface $component;


    public function __construct(?string $guiComponent = null, ?string $componentWrapper = WrapVisualObject::class)
    {
        $this->guiComponentClass = $guiComponent;
        $this->wrapComponentClass = $componentWrapper;
        //visual object factory create closure
        $this->createCall = fn(self $style) => WrappingVisualObjectFactory::create($style);
        //create child
        $this->afterCreateCall = fn() => is_null($this->child) ?: $this->child->create()->parent($this->component);

        parent::__construct();
    }


    public function child(Style $child): self
    {
        $this->child = $child;
        return $child->parent = $this;

    }

    public function __clone()
    {
        $this->child = clone $this->child;
    }


    public function create(): VisualObjectInterface
    {
        $this->component = ($this->createCall)($this);
        $this->attachOnEventActions();
        ($this->afterCreateCall)($this, $this->component);
        return $this->component;
    }

    protected function attachOnEventActions()
    {
        foreach ($this->on as $event => $onAction) {
            $this->component->on($event, fn() => $onAction($this->component));
        }
    }
}