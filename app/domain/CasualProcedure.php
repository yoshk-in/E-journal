<?php


namespace App\domain;

use App\base\AppMsg;
use DateTimeImmutable;

/**
 * @Entity
 *
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="class", type="string")
 * @DiscriminatorMap({"casual" = "CasualProcedure", "composite" = "CompositeProcedure"})
 *
 */
class CasualProcedure extends AbstractProcedure
{

    /**
     * @ManyToOne(targetEntity="Product")
     **/
    protected $owner;

    /** @Column(type="string") */
    protected $nameAfterEnd;

    public function __construct(string $name, int $idState, object $owner, string $nameAfterEnd)
    {
        parent::__construct($name, $idState, $owner);
        $this->nameAfterEnd = $nameAfterEnd;
    }

    public function start()
    {
        if ($this->isFinished()) {
            $this->getProduct()->nextProc($this);
            return;
        }
        parent::start();
    }

    public function end()
    {
        $this->checkInput((bool)$this->getStart(), ' событие еще не начато');
        $this->checkInput(!$end = $this->getEnd(), ' coбытие уже отмечено');
        $this->end = new DateTimeImmutable('now');
        $this->name = $this->nameAfterEnd;
        $this->getProduct()->procEnd($this);
        $this->changeStateToEnd();
    }



}