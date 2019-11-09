<?php


namespace App\GUI\startMode;


use App\base\AppMsg;
use App\GUI\Color;
use App\GUI\GUIManager;
use App\GUI\Response;
use Gui\Application;
use Gui\Components\Button;
use Gui\Components\InputNumber;
use Gui\Components\Label;
use Gui\Components\Shape;

class FirstStart extends StartMode
{

    private $nextMode;

    public function __construct(GUIManager $app, NotFirstStart $next)
    {
        parent::__construct($app);
        $this->nextMode = $next;
    }

    private $msg = 'номер должен состоять из шести цифр';

    function run(Response $response, Application $gui)
    {
        $shape = new Shape([
           'backgroundColor' => Color::WHITE,
           'width' => 300,
           'left' => 320,
           'top' => 170,
           'height' => 130
        ]);

        $label = new Label([
            'top' => 150,
            'left' => 400,
            'text' => 'Введите первый номер'
        ]);

        $input1 = (new InputNumber())
            ->setWidth(70)
            ->setHeight(50)
            ->setTop(200)
            ->setLeft(400)
            ->setValue(100)
            ->setMax(999);

        $input2 = (new InputNumber())
            ->setWidth(70)
            ->setHeight(50)
            ->setTop(200)
            ->setLeft(470)
            ->setValue(120000)
            ->setMax(999);

        $button = new Button([
            'left' => 470,
            'top' => 250,
            'value' => 'Ввести'
        ]);

        $button->on('mousedown', function () use ($input1, $input2, $label, $shape, $gui, $response, $button) {
            $value1 = $input1->getValue();
            $value2 = $input2->getValue();
            $value = $value1 . str_pad($value2, 3, 0,STR_PAD_LEFT);

            if (strlen($value) !== 6 || !is_int((int)$value) ) {
                $gui->alert($this->msg);
                return;
            }

            $request = $this->app->getRequest();
            $request->setBlockNumbers(range($value, (int)$value + $this->app->getProductsPerPage()));
            $response = $this->app->doRequest(AppMsg::CREATE_PRODUCTS);

            $gui->destroyObject($input1);
            $gui->destroyObject($input2);
            $gui->destroyObject($label);
            $gui->destroyObject($shape);
            $gui->destroyObject($button);
            $this->nextMode->run($response, $gui);

        });
    }
}