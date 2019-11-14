<?php


namespace App\GUI\components;


use App\GUI\factories\ButtonFactory;
use Gui\Components\Label;

class Pager
{
    private $bFactory;
    private $left = 100;
    private $top = 20;
    private $margin = 20;
    private $buttonHeight = 20;
    private $buttonWidth = 20;
    private $buttons = [];
    private $offset;


    public function __construct($bFactory = ButtonFactory::class)
   {
       $this->bFactory = $bFactory;
       $this->offset = $this->left;
   }

   public function addLabel()
   {
       new Label([
          'text' => 'листы:',
          'left' => $this->left - 60,
           'top' => $this->top
       ]);
   }

   public function add(\Closure $clickCallback)
   {
       $index = count($this->buttons);
       $text = $index + 1;
       $this->buttons[] = $button =
           $this->bFactory::create( $text, $this->offset, $this->top, $this->buttonHeight, $this->buttonWidth);
       $button->on('mousedown', function () use ($clickCallback, $index) {
           $clickCallback($index);
       });
       $this->offset += $this->buttonWidth + $this->margin;
   }
}