<?php

namespace App\domain;

use App\base\exceptions\AppException;
use App\base\exceptions\WrongInputException;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @Entity @HasLifeCycleCallbacks
 */
class Product implements Informer, IObservable
{
    use ProcedureCollectionValidator,
        TObservable;

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

    /** @Column(type="boolean") */
    protected $finished = false;

    private $initProcList;

    public function __construct(int $number, string $name, array $procedureList)
    {
        $this->number = $number;
        $this->name = $name;
        $this->initProcList = $procedureList;
        $this->procCollection = new ArrayCollection($this->initProcedures($procedureList));
    }


    public function getNameAndNumber(): array
    {
        return [$this->name, $this->number];
    }

    public function getName(): string
    {
        return $this->name;
    }


    public function getNumber(): int
    {
        return $this->number;
    }


    public function startProcedure(?string $partial = null)
    {
        $this->isNotFinishedCheck();
        if (is_null($this->currentProc)) {
            $this->currentProc = $this->procCollection->first();
        }
        $this->currentProc->setStart($partial);
    }

    public function nextProc(Procedure $proc)
    {
        $this->currentProc = $this->procCollection[$proc->getIdState() + 1];
        $this->startProcedure();
    }


    public function endProcedure()
    {
        $this->isNotFinishedCheck();
        $this->currentProc->setEnd();
        if ($this->currentProc === $this->procCollection->last()) $this->finished = true;
    }

    public function getCurrentProc(): Procedure
    {
        return $this->currentProc;
    }

    public function getInfo(): array
    {
        foreach ($this->procCollection as $proc) {
            if ($proc->getStart()) {
                $info[] = $proc->getInfo();
            } else break;
        }
        return $info ?? [];
    }


    protected function ensureRightInput($condition, string $msg = null): void
    {
        if (!$condition) throw new WrongInputException('ошибка: операция не выполнена: ' . $msg);
    }


    protected function initProcedures(array $procedureList): array
    {
        return ProcedureFactory::createProcedures($procedureList, $this);
    }


    protected function isNotFinishedCheck()
    {
        $this->ensureRightInput(!$this->finished, ' блок уже сдан на склад');
    }

}


