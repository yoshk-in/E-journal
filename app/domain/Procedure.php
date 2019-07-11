<?php


namespace App\domain;

use App\base\exceptions\IncorrectInputException;
use App\base\exceptions\WrongModelException;
use DateTimeImmutable;

/**
 * @MappedSuperClass
 */

abstract class Procedure
{
    /**
     *
     * @Column(type="datetime_immutable", nullable=true)
     **/
    protected $startProcedure;
    /**
     *
     * @Column(type="datetime_immutable", nullable=true)
     **/
    protected $endProcedure;
    /**
     *
     * @Column(type="string")
     **/
    protected $name;

    protected $idStage;
    /**
     * @Id
     * @Column(type="integer")
     **/
    protected $id;

    protected $product;

    protected function ensureRighInput(bool $condition, $msg = null): ?\Exception
    {
        if (!$condition) {
            throw new IncorrectInputException(
                'ошибка: операция не выполнена ' . $msg
            );
        }
        return null;
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
            $this->id = $idState;
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

    public function isFinished(): bool
    {
        if ($this->endProcedure) {
            return true;
        }
        return false;
    }

    public function getName(): string
    {
        return $this->name;
    }


    public function getStageId(): int
    {
        return $this->id;
    }

    public function getStart(): ?DateTimeImmutable
    {
        return $this->startProcedure;
    }

    public function getEnd(): ?DateTimeImmutable
    {
        return $this->endProcedure;
    }

    public function getProduct() : Product
    {
        return $this->product;
    }
}