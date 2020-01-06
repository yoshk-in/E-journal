<?php


namespace App\GUI\components;


use App\GUI\factories\WrappingVisualObjectFactory;
use App\GUI\grid\style\Style;
use Gui\Components\Button;
use Gui\Components\Label;
use Gui\Components\VisualObjectInterface;
use function App\GUI\cellStyle;

class Pager
{
    private array $buttons = [];
    private Style $buttonStyle;


    public function __construct()
   {
       $this->buttonStyle = cellStyle(new Style(Button::class), 100, 20, 20, 20);
       $this->buttonStyle->margin = 20;
       $this->buttonStyle->createCall = fn (Style $style) => WrappingVisualObjectFactory::create($style);
   }

   public function prepare()
   {
       new Label([
          'text' => 'листы:',
          'left' => $this->buttonStyle->left - 60,
           'top' => $this->buttonStyle->top
       ]);
   }

   public function addButton(\Closure $onClickHandler)
   {
       $index = count($this->buttons) + 1;
       $this->buttonStyle->value = $index;
       $this->buttonStyle->on = ['mousedown', fn (VisualObjectInterface $button) => $onClickHandler($button->getValue())];
       $this->buttons[] = $button = $this->buttonStyle->create();
       $this->buttonStyle->left += $this->buttonStyle->width + $this->buttonStyle->margin;
   }

   public function setVisible(bool $bool)
   {
       foreach ($this->buttons as $button)
       {
           $button->setVisible($bool);
       }
   }
}