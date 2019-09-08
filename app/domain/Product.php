<?php

namespace App\domain;

use App\base\exceptions\AppException;
use App\base\exceptions\IncorrectInputException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DateTimeImmutable;
use DateInterval;

/**
 * @Entity
 */
class Product
{

    /** @Id @Column(type="integer") @GeneratedValue */
    protected $id;

    /** @Column(type="integer") */
    protected $number;

    /** @OneToMany(targetEntity="Procedure", mappedBy="owner", cascade="persist", fetch="EAGER")
     * @OrderBy({"idState" = "desc"})
     */
    protected $procCollection;

    /** @Column(type="string") */
    protected $name;

    /** @OneToOne(targetEntity="Procedure", fetch="EAGER")
     * @JoinColumn(name="current_proc_id", referencedColumnName="id")
     */
    protected $currentProc;

    private $initProcList;

    public function __construct(int $number, string $name, array $procedureList)
    {
        $this->number = $number;
        $this->name = $name;
        $this->initProcList = $procedureList;
        $this->procCollection = new ArrayCollection($this->initProcedures($procedureList));
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getName()
    {
        return $this->name;
    }

    public function startProcedure(?string $partial = null): array
    {
        $this->ensureRightInput(!$this->isFinished(), ' блок уже сдан на склад');
        if (is_null($this->currentProc)) {
            $this->currentProc = $this->getProcCollection()->first();
        } else {
            $this->nextProc($partial);
        }
        return $this->_getInfo($this->getCurrentProc()->setStart($partial));
    }

    public function switchState(Procedure $proc): void
    {
        $next_state = $proc->getIdState() + 1;
        foreach ($this->procCollection as $proc) {
            if ($proc->getIdState() === $next_state) {
                ($this->currentProc = $proc);
                return;
            }
        }
        throw new AppException('неизвестная процедура');
    }

    public function endProcedure(): array
    {
        $this->ensureRightInput(!$this->isFinished(), ' блок уже сдан на склад');
        return $this->_getInfo($this->getCurrentProc()->setEnd());

    }


    public function getProcCollection(): Collection
    {
        return $this->procCollection;
    }

    public function getCurrentProc(): Procedure
    {
        return $this->currentProc;
    }

    public function getInfo(): array
    {
        return $this->_getInfo(
            $this->getProcCollection()->map(function ($proc) {
                return $proc->getInfo();
            })->toArray()
        );
    }

    public function getNameAndNumber(): array
    {
        return [$this->getName(), $this->getNumber()];
    }

    protected function nextProc(?string $partial): void
    {
        if ($partial) return;
        $current = $this->getCurrentProc();
        $this->ensureRightInput($current->isFinished(), 'нет отметки о завершении предыдущей процедуры - ' . $current->getName());
        $this->switchState($current);
    }


    protected function ensureRightInput($condition, string $msg = null): void
    {
        if (!$condition) throw new IncorrectInputException('ошибка: операция не выполнена: ' . $msg);
    }


    protected function isFinished(): bool
    {
        return $this->getProcCollection()->last()->isFinished();
    }

    protected function initProcedures(array $procedureList): array
    {
        return ProcedureFactory::createProcedures($procedureList, $this);
    }

    protected function _getInfo(array $info): array
    {
        return [[$this->getName(), $this->getNumber()], [$info]];
    }


}


