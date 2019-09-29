<?php


namespace App\domain;


use App\base\exceptions\WrongInputException;
use DateTimeImmutable;
use App\events\{TObservable, IObservable};

/**
 * @MappedSuperClass
 */
abstract class AbstractProcedure implements IObservable
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


    public function __construct(string $name, int $idState, object $owner)
    {
        $this->name = $name;
        $this->idState = $idState;
        $this->owner = $owner;
    }

    public function setStart()
    {
        $this->checkInput(is_null($this->start), 'coбытие уже отмечено');
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

    protected function getOwner()
    {
        return $this->owner;
    }

    protected function checkInput(bool $condition, $msg = null): ?\Exception
    {
        try {
            [$product, $number] = $this->getProduct()->getNameAndNumber();
            if (!$condition) throw new WrongInputException(
                printf("ошибка, операция не выполнена: блок %s, номер %s, процедура '%s': %s", $product, $number, $this->getName(), $msg)
            );
        } catch (\Exception $e) {
            exit;
        }

        return null;
    }

    protected function format(?\DateTimeInterface $time): string
    {
        return is_null($time) ? '' : $time->format('Y-m-d H:i:s');
    }

}
