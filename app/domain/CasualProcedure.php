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

    public function setStart()
    {
        if ($this->isFinished()) {
            $this->getProduct()->nextProc($this);
            return;
        }
        parent::setStart();
    }

    public function setEnd()
    {
        $this->checkInput((bool)$this->getStart(), ' событие еще не начато');
        $this->checkInput(!$end = $this->getEnd(), 'coбытие уже отмечено');
        $this->end = new DateTimeImmutable('now');
        $this->state = self::STAGE['end'];
        $this->notify(AppMsg::DISPATCH);
    }



}