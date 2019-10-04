<?php


namespace App\domain;

use DateTimeImmutable;

use App\events\Event;

/**
 * @Entity
 * @Table(name="`Procedure`")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="class", type="string")
 * @DiscriminatorMap({"casual" = "Procedure", "composite" = "CompositeProcedure"})
 *
 */
class Procedure extends AbstractProcedure
{

    /**
     * @ManyToOne(targetEntity="Product")
     **/
    protected $owner;


    public function setEnd()
    {
        $this->checkInput((bool)$this->getStart(), ' событие еще не начато');
        $this->checkInput(!$end = $this->getEnd(), 'coбытие уже отмечено');
        $this->end = new DateTimeImmutable('now');
        $this->notify(Event::END);
    }



}