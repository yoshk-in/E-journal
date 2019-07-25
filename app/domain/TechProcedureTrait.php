<?php

namespace App\domain;

use App\base\exceptions\WrongModelException;
use DateTimeImmutable;
use DateInterval;


trait TechProcedureTrait
{
    use ProcedureTrait;

    protected $interval;

    public function __construct()
    {
        if (!$this instanceof TechProcedure) throwException('this class must implements TechProcedure interface');
        parent::__construct();
    }

    public function setStart(): void
    {
        parent::setStart();
        if (is_null($this->interval)) {
            throw new WrongModelException('prop interval is required');
        }
        $this->endProcedure = $this->startProcedure->add($this->interval);
    }


    public function setInterval(string $interval): void
    {
        $this->interval = new DateInterval($interval);
    }

    public function getInterval(): DateInterval
    {
        return $this->interval;
    }

    public function isFinished(): bool
    {
        $now_time = new DateTimeImmutable('now');
        if (!is_null($this->endProcedure)) {
            if ($now_time > $this->endProcedure) {
                return true;
            }
        }
        return false;
    }

    public function getInfo(?string $short = null): string
    {
        $latin_name = $this->product->getTTProcedureList('ru')[$this->name];
        $format = $this->format_time;
        $finish_tense_word= ($this->isFinished()) ? 'завершено ' : 'завершится ';
        $string =
            ($short === 'short') ?
                $latin_name . ', '
                : 'испытание "' . $latin_name .'" - начато ' . $this->getStart()->format($format) .
                ", $finish_tense_word" . $this->getEnd()->format($format) ;
        return $string;
    }


}

