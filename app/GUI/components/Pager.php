<?php


namespace App\GUI\components;


use App\GUI\factories\ButtonFactory;
use Gui\Components\Label;
use function App\GUI\left;
use function App\GUI\offset;
use function App\GUI\size;
use function App\GUI\top;
use function App\GUI\width;

class Pager
{
    private $bFactory;
    private $betweenButtonSpace = 20;
    private $buttons = [];
    private $pagerOffset;
    private $offsets = [];
    private $sizes = [];



    public function __construct(ButtonFactory $bFactory)
   {
       $this->bFactory = $bFactory;
       $this->offsets = offset(100, 20);
       $this->pagerOffset = $this->offsets[IOffset::LEFT];
       $this->sizes = size(20, 20);
   }

   public function addTitle()
   {
//       $offsets = $this->offsets;
//       $offsets[IOffset::LEFT] = left($this->offsets) - 60;
       new Label([
          'text' => 'листы:',
          'left' => left($this->offsets) - 60,
           'top' => top($this->offsets)
       ]);
   }

   public function add(\Closure $onClick)
   {
       $index = count($this->buttons) + 1;
       $this->buttons[] = $button = $this->bFactory::create($this->offsets, $this->sizes, ['value' => $index]);
       $button->on('mousedown', \Closure::fromCallable($onClick));
       $this->offsets[IOffset::LEFT] += width($this->sizes) + $this->betweenButtonSpace;
   }

   public function setVisible(bool $bool)
   {
       foreach ($this->buttons as $button)
       {
           $button->setVisible($bool);
       }
   }
}