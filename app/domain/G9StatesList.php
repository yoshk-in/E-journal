<?php


namespace App\domain;

use DateTime;


class G9StatesList extends StatesList
{
    protected $statesArray = [
       1 => 'prozvon',
       2 => 'nastroy',
       3 => 'vibro',
       4 => 'progon',
       5 => 'moroz',
       6 => 'jara',
       7 => 'mechanikaOTK',
       8 => 'electrikaOTK',
       9 => 'mechanikaPZ',
       10 => 'electrikaPZ'
    ];
    protected $procedureList = [
        3 => '0.5h',
        4 => '2h',
        5 => '2h',
        6 => '2h'
    ];

    protected $statesList = array();
    protected $currentState;
    protected $currentStateNumber;
    protected $product;

    /**
     * G9StatesList constructor.
     * @param $product
     */
    public function __construct($product)
    {
        $this->product = $product;
    }

    public function setCurrentStateNumber($number = 0)
    {
        $this->currentStateNumber = $number;
    }


    public function nexState($nameState = null, $timeProcess = null)
    {
        $this->ensure(count($this->statesList) < count($this->statesArray), ' - продукт уже на складе');
        $now = new DateTime('now');
        if ($this->currentState instanceof Procedure) {
            $time = $this->currentState->getEndProcess();
            $this->ensure($time < $now, ', т.к. ' .
                'предыдущее испытание eще не закончено, время его завершения - ' . $time);
        }
        if ($this->currentStateNumber !== 0)  $this->currentState->setLeaving($now);
        ++$this->currentStateNumber;
        if (is_null($nameState)) $nameState = $this->getNameState();
        $newState = $this->getNewState($nameState, $timeProcess);
        $this->statesList[] = $newState;
        $this->currentState = $newState;
    }

    protected function getNameState() : string
    {
        return $this->statesArray[++$this->currentStateNumber];
    }

    protected function getNewState($name, $processTime = null)
    {
        if (array_key_exists($this->currentStateNumber, $this->procedureList)) {
            $this->ensure($processTime !== null, 'time process has not been set');
            return new Procedure($name, $this->product, $this->currentStateNumber, $processTime);
        }
        return new State($name, $this->product, $this->currentStateNumber);
    }

    public function getStatesList()
    {
        return $this->statesList;
    }

    public function ensure(bool $condition, string $msg = 'ошибка')
    {
        if (!$condition) throw new \App\base\AppException('ошибка: операция не выполнена' . $msg);
    }


}