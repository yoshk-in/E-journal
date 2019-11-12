<?php


namespace App\GUI\startMode;


use App\base\AppMsg;
use App\GUI\Color;
use Gui\Components\Button;
use Gui\Components\InputNumber;
use Gui\Components\Label;
use Gui\Components\Shape;

class FirstStart extends StartMode
{


    private $msg = 'номер должен состоять из шести цифр';



    function run()
    {
        $component['shape'] = new Shape([
           'backgroundColor' => Color::WHITE,
           'width' => 300,
           'left' => 320,
           'top' => 170,
           'height' => 130
        ]);

        $component['label'] = new Label([
            'top' => 150,
            'left' => 400,
            'text' => 'Введите первый номер'
        ]);

        $component['input1'] = (new InputNumber())
            ->setWidth(70)
            ->setHeight(50)
            ->setTop(200)
            ->setLeft(400)
            ->setValue(120)
            ->setMax(999);

        $component['input2'] = (new InputNumber())
            ->setWidth(70)
            ->setHeight(50)
            ->setTop(200)
            ->setLeft(470)
            ->setValue(100)
            ->setMax(999);

        $component['button'] = new Button([
            'left' => 470,
            'top' => 250,
            'value' => 'Ввести'
        ]);

        $component['button']->on('mousedown', function () use ($component) {
            $value1 = $component['input1']->getValue();
            $value2 = $component['input2']->getValue();
            $value = $value1 . str_pad($value2, 3, 0,STR_PAD_LEFT);

            if (strlen($value) !== 6 || !is_int((int)$value) ) {
                $this->app->alert($this->msg);
                return;
            }

            foreach ($component as $item)
            {
                $this->app->destroyObject($item);
            }

            $request = $this->app->getRequest();
            $request->setBlockNumbers(range($value, (int)$value + $this->app->getProductsPerPage()));
            $this->app->doRequest(AppMsg::CREATE_PRODUCTS);
        });
    }



}