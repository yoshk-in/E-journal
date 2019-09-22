<?php


namespace App\console;


use App\command\Command;
use App\command\FullInfoCommand;
use App\domain\AbstractProcedure;
use App\domain\ISubscriber;
use App\domain\Product;

class Render implements ISubscriber
{
    const THEME = [
        FullInfoCommand::class => 'найдена следующая информация:',
        Command::class => 'отмечены следующие события:',
    ];

    const TIME_FORMAT = 'd-m-Y H:i';


    const MODE = [
        'short' => 1,
        'long' => 2,
        'shortest' => 3
    ];

    protected $output;

    private $theme = '';
    private $renderMode;
    private $end = [
        'did' => 'завершена',
        'will' => 'завершится'
    ];
    private $start = 'начата';
    private $procPatternInfo = [
        Render::MODE['long'] => "> %-22s  - процедура %s %s %s",
        Render::MODE['short'] => '[ %-22s ] - процедура %s'
    ];
    private $partialsPatternInfo = '       * завершенные подпроцедуры: ';

    private $number = ' Номер %s';
    private $product = ' Блок %s';
    private $stat_string = '%s - %s штук: ';
    private $stat;

    public function __construct()
    {
        $this->renderMode = Render::MODE['long'];
    }

    public function flush()
    {
        $this->printHeader();
        if (is_null($this->output)) return;
        foreach ($this->output as $product => $numbers) {
            $this->printProductName($product);
            foreach ($numbers as $number => $procedures) {
                $this->printProductNumber($number);
                $this->printProcedures($procedures);
                print PHP_EOL;
            }

        }
      $this->printStat();
    }

    private function printHeader()
    {
        print $this->theme . PHP_EOL . PHP_EOL;
    }

    private function printStat()
    {
        if ($this->theme !== Render::THEME[FullInfoCommand::class]) return;

        print 'Итого:' . PHP_EOL ;
        $total = 0;
        foreach ($this->stat as $product => $procedures) {
            foreach ($procedures as $name => $product_numbers) {
                printf($this->stat_string, $name, $part = count($product_numbers));
                $total += $part;
                foreach ($product_numbers as $key => $number) {
                    print $number . $this->getConjuntion($product_numbers, $key, [PHP_EOL, ', ']);
                }
            }
        }
        printf( 'Всего %s штук' . PHP_EOL, $total);
    }

    private function getConjuntion(array $array, int $currentKey, array $conjuctions)
    {
        if (array_key_last($array) === $currentKey) return $conjuctions[0];
        else return $conjuctions[1];
    }

    private function printProductName(string $product): void
    {
        printf($this->product . PHP_EOL . PHP_EOL, $product);
    }

    private function printProductNumber(string $number): void
    {
        printf($this->number . PHP_EOL, $number);
    }

    private function printProcedures(?array $procedures = null, array $conjunction = [PHP_EOL, PHP_EOL]): void
    {
        if (is_null($procedures)) return;

        foreach ($procedures as $key => $procOrPartials) {
            if (is_array($procOrPartials)) {
                printf($this->partialsPatternInfo);
                $this->printProcedures($procOrPartials, [PHP_EOL, ', ']);
            } else {
                printf($procOrPartials . $this->getConjuntion($procedures, $key, $conjunction));
            }
        }
    }


    public function update($object)
    {
        switch ($object) {
            case $object instanceof Product:
                $product = $object;
                $this->theme = self::THEME[FullInfoCommand::class];
                foreach ($product->getInfo() as $procedure) {
                    $this->handleProcedureInfo($product, ...$procedure);
                }
                $this->makeStat($product);
                break;
            case $object instanceof AbstractProcedure:
                $this->theme = self::THEME[Command::class];
                $this->handleProcedureInfo($object->getProduct(),...$object->getInfo());
        }
    }

    protected function handleProcedureInfo(
        Product $product,
        string $name,
        ?\DateTimeInterface $start,
        ?\DateTimeInterface $end,
        ?bool $finished,
        ?array $inners = null
    ): void  {

        if (is_null($start)) return;

        [$prod_name, $prod_number] = $product->getNameAndNumber();
        $this->output[$prod_name][$prod_number][] = $this->renderProcInfo(Render::MODE['long'], $name, $start, $end, $finished);

        if (!is_null($inners)) {
            $partials_key = array_key_last($this->output[$prod_name][$prod_number]) + 1;
            $this->renderMode = self::MODE['shortest'];
            foreach ($inners as $inner_proc) {
                $this->output[$prod_name][$prod_number][$partials_key][] = $this->renderProcInfo(Render::MODE['shortest'], ...$inner_proc);
            }
        }
    }

    private function renderProcInfo($renderMode, $name, $start, $end, $finished)
    {
        switch ($renderMode) {
            case $mode = Render::MODE['long']:
                return sprintf(
                    $this->procPatternInfo[$mode],
                    $name,
                    $this->start,
                    $this->timeToStr($start),
                    $this->getEndPart($finished, $end),
                );
            case $mode = Render::MODE['short']:
                return sprintf(
                    $this->procPatternInfo[$mode],
                    $name,
                    ($this->getEndPart($finished, $end) ?: $this->start . $this->timeToStr($start))
                );
            case $mode = Render::MODE['shortest']:
                if (!$finished) return '';
                return $name;
        }
    }

    private function makeStat(Product $product)
    {
        $this->stat[$product->getName()][$product->getCurrentProc()->getName()][] = $product->getNumber();
    }


    private function getEndPart(bool $finished, $end)
    {
        $end_part = $finished ? $this->end['did'] : $this->end['will'];
        return $end ? sprintf($end_part . '  %s', $this->timeToStr($end)) : '';
    }

    private function timeToStr(\DateTimeInterface $time)
    {
        return $time->format(self::TIME_FORMAT);
    }
}