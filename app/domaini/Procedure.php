<?php


namespace App\domaini;

use App\base\exceptions\IncorrectInputException;
use App\base\exceptions\WrongModelException;
use DateTimeImmutable;


/**
 * @Entity @Table(name="states")
 *
 **/
class Procedure
{
    protected $startProcedure;
    protected $endProcedure;
    protected $name;
    protected $product;
    protected $idStage;


    public function __construct()
    {
    }

    public function setIdentityData(
        string $name, Product $product, int $idState
    ): void
    {
        if (!($product instanceof Product)) {
            throw new WrongModelException('own object must be instance of Product');
        }
        if (is_null($this->name)
            && is_null($this->product)
            && is_null($this->idStage)
        ) {
            $this->name = $name;
            $this->product = $product;
            $this->idStage = $idState;
            return;
        }
        throw new WrongModelException('identity data already is set');
    }

    public function setStart(): void
    {
        $not_inited = (is_null($this->name) && is_null($this->product) && is_null($this->idStage));
        if ($not_inited) {
            throw new WrongModelException('object is not inited');
        }
        $this->ensureRighInput(
            is_null($this->startProcedure),
            'данное событие уже отмечено в журнале'
        );
        $this->startProcedure = new DateTimeImmutable('now');
    }

    public function getStart(): ?DateTimeImmutable
    {
        return $this->startProcedure;
    }

    public function getEnd(): ?DateTimeImmutable
    {
        return $this->endProcedure;
    }


    public function setEnd(): void
    {
        $this->ensureRighInput(
            !is_null($this->startProcedure),
            'в журнале нет отметки' .
            ' о начале данной процедуры '
        );
        $this->ensureRighInput(
            is_null($this->endProcedure), 'данное событие уже отмечено в журнале'
        );
        $this->endProcedure = new DateTimeImmutable('now');
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function getStageId(): int
    {
        return $this->idStage;
    }

    public function isFinished(): bool
    {

        if ($this->endProcedure) {
            return true;
        }
        return false;
    }

    protected function ensureRighInput(bool $condition, $msg = null): ?\Exception
    {
        if (!$condition) {
            throw new IncorrectInputException(
                'ошибка: операция не выполнена ' . $msg
            );
        }
        return null;
    }

}