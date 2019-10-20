<?php


namespace App\GUI;


use Gui\Components\VisualObjectInterface;

class RowAligner
{
    protected $cells = [];
    protected $height;
    protected $top;
    protected $offset;
    protected $left;


    public function addCell(VisualObjectInterface $cell): RowAligner
    {
        $this->cells[] = $cell;
        $cell->setLeft($this->offset)
            ->setHeight($this->height)
            ->setTop($this->top)
            ->alignText();
        $this->offset += $cell->getWidth();
        return $this;
    }

    /**
     * @param mixed $height
     * @return RowAligner
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @param mixed $top
     * @return RowAligner
     */
    public function setTop($top)
    {
        $this->top = $top;
        return $this;
    }

    /**
     * @param mixed $offset
     * @return RowAligner
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @param mixed $left
     * @return RowAligner
     */
    public function setLeft($left)
    {
        $this->left = $left;
        return $this;
    }


}