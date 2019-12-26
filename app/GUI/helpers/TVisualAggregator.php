<?php


namespace App\GUI\helpers;


trait TVisualAggregator
{
    protected bool $visible = true;

    public function setVisible(bool $bool): self
    {
        if ($this->visible == $bool) return $this;
        array_map(function ($item) use ($bool) {
            $item->setVisible($bool);
        }, $this->getVisualComponents());
        $this->visible = $bool;
        return $this;
    }

    abstract protected function getVisualComponents(): array;
}