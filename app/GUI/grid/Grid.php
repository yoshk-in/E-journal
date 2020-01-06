<?php


namespace App\GUI\grid;


use App\GUI\components\IOffset;
use App\GUI\components\ISize;
use App\GUI\grid\coordinate\GridPosition;
use App\GUI\grid\style\BasicVisualStyle;
use App\GUI\grid\style\Style;
use App\GUI\grid\traits\TEventEmitter;
use function App\GUI\offset;
use function App\GUI\size;

class Grid implements IOffset, ISize
{
    use TEventEmitter;

    protected GridPosition $grid;
    protected int $xLine = -1;
    protected int $yLine = -1;
    protected BasicVisualStyle $windowSize;
    protected Grid $aligning;



    const EVENT = [
        'render' => 'render',
    ];

    public function __construct(BasicVisualStyle $style, BasicVisualStyle $windowSize)
    {
        $this->grid = new GridPosition();
        $style->width = 0;
        $style->height = 0;
        $this->windowSize = $windowSize;
    }

    protected function putInX(Style $style)
    {
        $this->grid->x[++$this->xLine] = $style;
    }

    protected function putInY(Style $style)
    {
        $this->grid->y[++$this->yLine] = $style;
    }


    public function render()
    {
        $this->startCell->create( 'right', $this, -1);
        $this->fire(__FUNCTION__);
    }


    public function pushCreate(int $parentId, \Closure $createClosure, array $childSizes, string $direction): int
    {
        $offsets = $this->computeChildOffsets($parentId, $direction);
        return $this->createCell($createClosure, $offsets, $childSizes);
    }



    protected function computeChildOffsets(int $parentId, string $direction): array
    {
        $key = self::DIRECTION_TO_OFFSET[$direction];
        [$offsets, $parentSizes] = $this->getCellSizes($parentId);
        $offsets[$key] = $this->value($offsets, $key) + $this->value($parentSizes, self::OFFSET_TO_SIZE[$key]);
        return $offsets;
    }


    protected function createCell(\Closure $createClosure, array $offsets, array $sizes): int
    {
        $this->_createCell($createClosure, $offsets);
        $this->grid[++$this->cellCounter] = [$offsets, $sizes];
        return $this->cellCounter;
    }

    protected function value(array $array, string $key)
    {
        assert(array_key_exists($key, $array), $this->wrongKey);
        return $array[$key];
    }

    protected function _createCell($createClosure, $offsets)
    {
        $createClosure($offsets);
    }

    protected function getCellSizes(int $id): array
    {
        return $this->value($this->grid, (string) $id);
    }



}