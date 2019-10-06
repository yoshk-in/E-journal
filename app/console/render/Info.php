<?php


namespace App\console\render;


use App\domain\AbstractProcedure;
use App\domain\Product;
use Doctrine\Common\Collections\Collection;

abstract class Info
{
    const TIME = 'd-m-Y H:i';
    const PRODUCT_NUMBER = ' Номер [ %s ]';
    const PRODUCT_NAME = ' Блок %s';
    const STAT = '%s - %s штук: %s';
    const STAT_TITLE = 'Всего %s штук';
    const FULL = "| %'_-25s вр.начала: %s, вр.завершения: %s";
    const COMPOSITE_CONJUCTION = '   <вложенные процедуры: ';
    const EOL = PHP_EOL;
    const SHORT = ' %s вр. заверш: %s ';
    const COMMA = ', ';
    const HYPHEN = ' - ';
    const SEMICOLON = '; ';

    protected $output = '';
    protected $statTitle;
    protected $prodNamePattern;
    protected $prodNumberPattern;



    protected function getEnd(AbstractProcedure $proc): string
    {
        return $proc->getEnd() ? $proc->getEnd()->format(Info::TIME) :  Info::HYPHEN;
    }


    protected function getStart(AbstractProcedure $proc): string
    {
        return $proc->getStart()? $proc->getStart()->format(Info::TIME) :  Info::HYPHEN;
    }


    protected function makeStat(Collection $products): array
    {
        foreach ($products as $product) {
            $stat[$product->getCurrentProc()->getName()][] = $product->getNumber();
        }
        return $stat ?? [];
    }

    public function clear()
    {
        $this->output = '';
    }

    public function formatStat(Collection $products) : string
    {
        $this->output .= sprintf($this->statTitle, $products->count());
        $stat = $this->makeStat($products);
        foreach ($stat as $proc_name => $numbers) {
            $this->output .= sprintf(
                Info::STAT,
                $proc_name,
                count($numbers),
                implode(Info::COMMA, $numbers)
            );
        }
        return $this->output;
    }



    protected function productNumberStr(Product $product)
    {
        return sprintf($this->prodNumberPattern, $product->getNumber());
    }




}