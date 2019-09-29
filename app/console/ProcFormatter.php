<?php


namespace App\console;



use App\domain\AbstractProcedure;

class ProcFormatter implements Format
{

    const TIME_FORMAT = 'd-m-Y H:i';

    protected $output;

    private $end = [
        'did' => 'завершена',
        'will' => 'завершится'
    ];
    private $start = 'начата';
    private $procPatternInfo = [
        Format::LONG => " %-22s  процедура %s %s %s",
        Format::SHORT => ' %-22s  - процедура %s'
    ];

    private $partialsPatternInfo = '       * завершенные подпроцедуры: ';

    private $number = ' Номер [ %s ]';
    private $product = ' Блок %s';
    private $stat_string = '%s - %s штук: ';
    private $stat_block = 'Итого:' . PHP_EOL . 'Всего %s штук';


    public function getProcInString(int $format, AbstractProcedure $procedure)
    {
        if (!$procedure->getStart()) return null;

        switch ($format) {
            case $mode = Format::LONG:
                return sprintf(
                    $this->procPatternInfo[$mode],
                    $procedure->getName(),
                    $this->start,
                    $this->timeToStr($procedure->getStart()),
                    $this->getEndPart($procedure),
                );
            case $mode = Format::SHORT:
                return sprintf(
                    $this->procPatternInfo[$mode],
                    $procedure->getName(),
                    ($this->getEndPart($procedure) ?: $this->start . $this->timeToStr($procedure->getStart()))
                );
            case $mode = Format::SHORTEST:
                if (!$procedure->isFinished()) return '';
                return $procedure->getName();
        }
    }

    private function getEndPart(AbstractProcedure $procedure)
    {
        if (!$procedure->getEnd()) return '';
        $end_part = $procedure->isFinished() ? $this->end['did'] : $this->end['will'];
        return sprintf($end_part . '  %s', $this->timeToStr($procedure->getEnd()));
    }

    private function timeToStr(\DateTimeInterface $time)
    {
        return $time->format(self::TIME_FORMAT);
    }

    public function printProductName(string $product): void
    {
        printf($this->product . PHP_EOL . PHP_EOL, $product);
    }

    public function printProductNumber(string $number): void
    {
        printf($this->number . PHP_EOL, $number);
    }

    public function printHeader(string $header)
    {
        print $header . PHP_EOL . PHP_EOL;
    }

}