<?php


namespace App\domain;


use App\base\AppMsg;
use App\base\exceptions\WrongInputException;
use App\events\{IListenable, IObservable, TListenable, TObservable};
use DateTimeImmutable;


abstract class AbstractProcedure implements IObservable, IListenable
{
    use TListenable;

    /** @Column(type="datetime_immutable", nullable=true)    */
    protected $start;

    /** @Column(type="datetime_immutable", nullable=true)   */

    protected $end;

    /** @Column(type="string")                              */
    protected $name;

    /** @Column(type="integer")                             */
    protected $idState;

    /** @Column(name="state", type="integer")               */
    protected $state = 0;

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
        $this->notify(AppMsg::ARRIVE);
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

    protected function getOwner()
    {
        return $this->owner;
    }

    protected function _setStart()
    {
        $this->checkInput(is_null($this->start), 'coбытие уже отмечено');
        $this->start = new DateTimeImmutable('now');
        $this->state = self::STAGE['start'];
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
