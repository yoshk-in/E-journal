<?php


namespace App\domain;

use App\base\exceptions\IncorrectInputException;
use App\base\exceptions\WrongModelException;
use DateTimeImmutable;


trait ProcedureTrait
{
    /**
     *
     * @Column(type="datetime", nullable=true)
     **/
    protected $startProcedure;
    /**
     *
     * @Column(type="datetime", nullable=true)
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

    protected $format_time = "Y-m-d H:i";



    public function setEnd(): void
    {
        $this->ensureRighInput(
            (bool)($this->startProcedure),
            'в журнале нет отметки' .
            ' о начале данной процедуры '
        );
        $this->ensureRighInput(
            is_null($this->endProcedure), 'данное событие уже отмечено в журнале'
        );
        $this->endProcedure = new DateTimeImmutable('now');
    }

    public function getInfo(?string $option): string
    {
        $latin_name = $this->product->getProcedureList('ru')[$this->name];
        $format = $this->format_time;
        $string = "" . $latin_name . "";
        $finish_tense_word = ($this->isFinished()) ? 'завершена ' : 'завершится ';
        if ($this->getStart() && (!$this->getEnd())) {
            $string .= ' начата ' . $this->getStart()->format($format);
        } else {
            $string .= (!$option === 'all') ?: ' начата ' . $this->getStart()->format($format) . ', ';
            $string .= $finish_tense_word . $this->getEnd()->format($format);
        }
        return $string;
    }


}