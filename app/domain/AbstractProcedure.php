<?php


namespace App\domain;


use App\base\exceptions\IncorrectInputException;
use DateTimeImmutable;

/**
 * @MappedSuperClass
 */
class AbstractProcedure
{
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


    public function __construct(string $name, int $idState, object $owner)
    {
        $this->name = $name;
        $this->idState = $idState;
        $this->owner = $owner;
    }

    public function setStart(): array
    {
        $this->ensureRightInput(
            is_null($start = $this->start),
            "данное событие уже отмечено в журнале, начато " .
            "{$this->format($start)}" . (($end = $this->getEnd()) ? ", заверешено {$this->format($end)}" : '' )
        );
        $this->start = new DateTimeImmutable('now');
        return $this->getInfo();
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function getProduct()
    {
        return $this->getOwner();
    }

    public function getIdState() : int
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

    public function getStart(): ?DateTimeImmutable
    {
        return $this->start;
    }

    public function getEnd(): ?DateTimeImmutable
    {
        return $this->end;
    }


    public function getInfo(): array
    {
        return [
            $this->name,
            $this->format($this->start),
            $this->format($this->end),
            $this->isFinished()
        ];
    }

    protected function ensureRightInput(bool $condition, $msg = null): void
    {
        $product = $this->getProduct()->getName();
        $number = $this->getProduct()->getNumber();
        if (!$condition) throw new IncorrectInputException("ошибка, операция не выполнена: блок $product, номер $number, процедура '{$this->getName()}'; " . $msg);
    }

    protected function format(?DateTimeImmutable $time)
    {
        return is_null($time)? null : $time->format('Y-m-d H:i:s');
    }
}
