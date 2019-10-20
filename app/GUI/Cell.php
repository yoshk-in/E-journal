<?php


namespace App\GUI;


use Gui\Components\Label;
use Gui\Components\Shape;
use Gui\Components\VisualObjectInterface;

class Cell implements VisualObjectInterface
{
    private $label;
    private $shape;


    public function __construct()
    {
        $this->shape = new Shape();
        $this->label = new Label();
    }

    public function getLabel(): Label
    {
        return $this->label;
    }


    public function getShape(): Shape
    {
        return $this->shape;
    }

    public function getWidth()
    {
        return $this->shape->getWidth();
    }

    public function setWidth($width): self
    {
        $this->shape->setWidth($width);
        return $this;
    }

    public function getHeight()
    {
        return $this->shape->getHeight();
    }

    public function setHeight($height): self
    {
        $this->shape->setHeight($height);
        return $this;
    }


    public function getTop()
    {
        return $this->shape->getTop();
    }


    public function setTop($top): self
    {
        $this->shape->setTop($top);
        return $this;
    }

    public function getLeft()
    {
        return $this->shape->getLeft();
    }


    public function setLeft($left): self
    {
        $this->shape->setLeft($left);
        return $this;
    }



    public function setText(string $text): self
    {
        $this->label->setText($text);
        return $this;
    }

    public function setBackgroundColor($color): self
    {
        $this->shape->setBorderColor($color);
        return $this;
    }

    public function alignText()
    {
        $left = ($this->shape->getWidth() - $this->label->getWidth()) / 2;
        $top = ($this->shape->getHeight() - $this->label->getHeight()) / 2;
        $this->label->setTop($this->shape->getTop() + $top);
        $this->label->setLeft($this->shape->getLeft() + $left);
        return $this;
    }


    public function getAutoSize()
    {
        // TODO: Implement getAutoSize() method.
    }

    public function setAutoSize($autoSize)
    {
        // TODO: Implement setAutoSize() method.
    }


    public function getBackgroundColor()
    {
        return $this->shape->getBackgroundColor();
    }


    public function getBottom()
    {
        // TODO: Implement getBottom() method.
    }


    public function setBottom($bottom): self
    {
        $this->shape->setBottom($bottom);

        return $this;
    }


    public function getRight()
    {
        return $this->shape->getRight();
    }


    public function setRight($right): self
    {
        $this->shape->setRight($right);

        return $this;
    }

    /**
     * Gets the value of visible in pixel.
     *
     * @return boolean
     */
    public function getVisible()
    {
        // TODO: Implement getVisible() method.
    }

    /**
     * Sets the value of visible in pixel.
     *
     * @param boolean $visible the visible
     *
     * @return self
     */
    public function setVisible($visible)
    {
        // TODO: Implement setVisible() method.
    }


    public function setBorderColor(string $borderColor): self
    {
        $this->shape->setBorderColor($borderColor);

        return $this;
    }

    public function setFontColor(string $fontColor): self
    {
        $this->label->setFontColor($fontColor);
        return $this;
    }

    public function getFontColor()
    {
        return $this->label->getFontColor();
    }


    public function setFontSize($fontSize): self
    {
        $this->label = $fontSize;

        return $this;
    }
}