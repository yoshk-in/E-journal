<?php


namespace App\console;


use App\command\Command;
use App\command\FullInfoCommand;
use App\domain\AbstractProcedure;
use App\domain\Event;
use App\domain\ISubscriber;
use App\domain\Product;

class Render implements ISubscriber, Format, Event
{
    const THEME = [
        FullInfoCommand::class => 'найдена следующая информация:',
        Command::class => 'отмечены следующие события:',
        Event::START => 'начаты следующие процедуры:',
        Event::END => 'завершены следующие процедуры',
        Event::START_THEN_END => 'начаты следующие процедуры:'
    ];

    protected $output;

    private $theme = '';

    private $partialsPatternInfo = '       * завершенные подпроцедуры: ';

    private $stat_string = ' %s - %s штук: ';
    private $stat;
    private $formatter;
    const MARKS = [
        'enter' => [PHP_EOL, PHP_EOL],
        'comma' => [', ', PHP_EOL]
    ];

    private $product;

    public function __construct(ProcFormatter $stringFormatter)
    {
        $this->formatter = $stringFormatter;
    }

    public function flush()
    {
        $this->formatter->printHeader($this->theme);
        if (is_null($this->output)) return;
        foreach ($this->output as $product => $numbers) {
            $this->formatter->printProductName($product);
            foreach ($numbers as $number => $procedures) {
                $this->formatter->printProductNumber($number);
                $this->printProcedures($procedures);

            }

        }
        $this->printStat();
    }

    private function printStat()
    {
        if ($this->theme !== Render::THEME[FullInfoCommand::class]) return;

        $total = 0;

        foreach ($this->stat as $product => $procedures) {
            foreach ($procedures as $name => $product_numbers) {
                $stat_block = sprintf($this->stat_string, $name, $part = count($product_numbers));
                $total += $part;
                foreach ($product_numbers as $key => $number) {
                    $stat_block .= $number . $this->getConjuntion($product_numbers, $key, self::MARKS['enter']);
                }
            }
        }
        printf('Итого:' . PHP_EOL . ($stat_block ?? ''). 'Всего %s штук' . PHP_EOL, $total);
    }

    private function printProcedures(?array $procedures = null, array $marks = self::MARKS['enter']): void
    {
        if (empty($procedures)) return;

        foreach ($procedures as $key => $procOrPartials) {
            if (is_array($procOrPartials)) {
                printf($this->partialsPatternInfo);
                $this->printProcedures($procOrPartials,self::MARKS['comma']);
            } else {
                printf($procOrPartials . $this->getConjuntion($procedures, $key, $marks));
            }
        }
        print PHP_EOL;
    }

    public function update($object, string $event)
    {
        $this->theme = self::THEME[$event];
        switch ($object) {
            case $object instanceof Product:
                foreach ($object->getProcedures() as $procedure) {
                    $this->handleProcedureInfo($object, $procedure);
                }
                $this->makeStat($object);
                break;
            case $object instanceof AbstractProcedure:
                $this->handleProcedureInfo($object->getProduct(), $object);
        }
    }


    protected function handleProcedureInfo(Product $product, AbstractProcedure $procedure): void
    {
        if (!$procedure->getStart()) return;

        [$prod_name, $prod_number] = $product->getNameAndNumber();
        $this->output[$prod_name][$prod_number][] = $this->formatter->getProcInString(Format::LONG, $procedure);

        if (!$procedure->isComposite()) return;

        $partials_key = array_key_last($this->output[$prod_name][$prod_number]) + 1;

        foreach ($procedure->getInners() as $inner_proc) {
            $partial_info = $this->formatter->getProcInString(Format::SHORTEST, $inner_proc);
            if (!$partial_info) continue;
            $this->output[$prod_name][$prod_number][$partials_key][] = $partial_info;
        }

    }


    private function makeStat(Product $product)
    {
        $this->stat[$product->getName()][$product->getCurrentProc()->getName()][] = $product->getNumber();
    }


    private function getConjuntion(array &$array, int $currentKey, array $type) : string
    {
        if (array_key_last($array) === $currentKey) return $type[1];
        else return $type[0];
    }

}