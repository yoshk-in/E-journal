<?php


namespace App\domain;


use App\base\exceptions\WrongInputException;
use DateTimeImmutable;

/**
 * @MappedSuperClass
 */
class AbstractProcedure implements IObservable, Informer
{
    use TObservable;

    /** @Column(type="datetime_immutable", nullable=true) */
    protected $start;

    /** @Column(type="datetime_immutable", nullable=true) */

    protected $end;

    /** @Column(type="string") */
    protected $name;

    /** @Column(type="integer") */
    protected $idState;

    /** @Id @Column(type="integer") @GeneratedValue */
    protected $id;

    protected $owner;

    const ERRORS = [
        'early_end_casual' => ['нет отметки о начале данной процедуры', true],
        'early_end_composite' => ['нет отметки о завершении внутренних процедур: ', false],
        'repeat' => ['данное событие уже отмечено в журнале ', true],
        'inner_not_fount' => ['не найдено вложенной процедуры под именем ', false]
    ];


    public function __construct(string $name, int $idState, object $owner)
    {
        $this->name = $name;
        $this->idState = $idState;
        $this->owner = $owner;
    }

    public function setStart()
    {
        $this->checkInput(is_null($this->start), $this->errorStr('repeat'));
        $this->start = new DateTimeImmutable('now');
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
        return ($this->end ? ((new DateTimeImmutable('now') > $this->end) ?: false) : false);
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

    public function getInfo(): array
    {
        return [$this->name, $this->start, $this->end, $this->isFinished()];
    }

    protected function getInfoToExcept(): array
    {
        return [$this->name, $this->format($this->start), $this->format($this->start)];
    }

    protected function getOwner()
    {
        return $this->owner;
    }

    protected function checkInput(bool $condition, $msg = null): ?\Exception
    {
        [$product, $number] = $this->getProduct()->getNameAndNumber();
        if (!$condition) throw new WrongInputException("ошибка, операция не выполнена: блок $product, номер $number, процедура '{$this->getName()}': $msg");
        return null;
    }

    protected function format(?\DateTimeInterface $time): string
    {
        return is_null($time) ? '' : $time->format('Y-m-d H:i:s');
    }

    protected function errorStr(string $type_error): string
    {
        $main = self::ERRORS[$type_error][0];
        $advance = self::ERRORS[$type_error][1] ?
            "начато " . $this->format($this->start) .
            ($this->end ? ", завершено " . $this->format($this->end) : '')
        : '';
        return "\n$main\n$advance\n";

    }
}
