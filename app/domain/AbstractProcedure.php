<?php


namespace App\domain;


use App\base\AppMsg;
use App\base\exceptions\WrongInputException;
use App\events\{IObservable,  TObservable};
use DateTimeImmutable;


abstract class AbstractProcedure implements IObservable
{
    use TObservable;

    /** @Column(type="datetime_immutable", nullable=true)    */
    protected $start;

    /** @Column(type="datetime_immutable", nullable=true)   */

    protected $end;

    /** @Column(type="string")                              */
    protected $name;

    /** @Column(type="integer")                             */
    protected $idState;

    /** @Column(name="state", type="integer")               */
    protected $state = self::STAGE['not_start'];

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    const STAGE = [
        'not_start' => 0,
        'start' => 1,
        'end' => 2
    ];

    protected static $startEvent = AppMsg::ARRIVE;
    protected static $endEvent = AppMsg::DISPATCH;

    protected $owner;

    public function __construct(string $name, int $idState, object $owner)
    {
        $this->name = $name;
        $this->idState = $idState;
        $this->owner = $owner;
    }

    public function start()
    {
        $this->_setStart();
        $this->getProduct()->procStart($this);
        $this->changeStateToStart();
    }

    public function getProduct(): Product
    {
        return $this->getOwner();
    }

    public function getIdState(): int
    {
        return $this->idState;
    }

    public function isFinished(): bool
    {
        return $this->state === self::STAGE['end'];
    }

    public function isNotStarted(): bool
    {
        return $this->getState() === self::STAGE['not_start'];
    }

    public function isStarted(): bool
    {
        return $this->getState() === self::STAGE['start'];
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function getState() : int
    {
        return $this->state;
    }

    final protected function changeStateToStart()
    {
        $this->state = self::STAGE['start'];
        $this->notify(self::$startEvent);
    }

    final protected function changeStateToEnd()
    {
        $this->state = self::STAGE['end'];
        $this->notify(self::$endEvent);
    }


    protected function getOwner()
    {
        return $this->owner;
    }

    protected function _setStart()
    {
        $this->checkInput(is_null($this->start), 'coбытие уже отмечено');
        $this->start = new DateTimeImmutable('now');
    }


    protected function checkInput(bool $condition, $msg = null): ?\Exception
    {
        try {
            [$product, $number] = $this->getProduct()->getNameAndNumber();
            if (!$condition) throw new WrongInputException(
                printf("ошибка, операция не выполнена: блок %s, номер %s, процедура '%s': %s \n", $product, $number, $this->getName(), $msg)
            );
        } catch (\Exception $e) {
            exit;
        }

        return null;
    }


}
