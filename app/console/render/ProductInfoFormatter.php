<?php


namespace App\console\render;


use App\domain\AbstractProcedure;
use App\domain\CompositeProcedure;
use App\domain\PartialProcedure;
use App\domain\Procedure;


class ProductInfoFormatter extends Info
{
    const PROC_PATTERN              = Info::FULL . Info::EOL;
    protected $prodNamePattern      = Info::PRODUCT_NAME . Info::EOL;
    protected $prodNumberPattern    = Info::PRODUCT_NUMBER . Info::EOL;
    private $compProcPattern        = Info::FULL . Info::COMPOSITE_CONJUCTION;
    private $delimiter              = '';
    private $partialProcPattern     = Info::SHORT;
    protected $statTitle            = Info::STAT_TITLE . Info::EOL;

    private $byDepthProcFormat0     = 'fullFormatProc';
    private $byDepthCompFormat0     = 'fullFormatComposite';
    private $byDepthPartFormat1     = 'shortFormatProc';

    const FOR_PARTIAL               = 'Part';
    const FOR_COMPOSITE             = 'Comp';
    const FOR_PROC                  = 'Proc';

    protected function fullFormatProc(AbstractProcedure $procedure, string $pattern = self::PROC_PATTERN)
    {
        return sprintf($pattern, $procedure->getName(), $this->getStart($procedure), $this->getEnd($procedure));
    }

    public function shortFormatProc(AbstractProcedure $proc)
    {
        $this->setDelimiter(Info::EOL);
        return sprintf($this->partialProcPattern, $proc->getName(), $this->getEnd($proc));
    }

    public function fullFormatComposite(AbstractProcedure $procedure)
    {
        return $this->fullFormatProc($procedure, $this->compProcPattern);
    }


    public function formatProducts(\ArrayAccess $products): string
    {
        foreach ($products as $product) {
            $this->output .= $this->productNumberStr($product);
            $this->formatProcCollection($product->getProcedures());
            $this->output .= Info::EOL;
        }
        return $this->output;
    }

    public function formatProcedure(AbstractProcedure $procedure)
    {
        $this->output .= $this->productNumberStr($procedure->getProduct());
        $this->output .= $this->fullFormatProc($procedure);
        return $this->output;
    }

    public function setFormatForProducts(string $procFormat, string $compProcFormat, string $partialProcFormat)
    {
        $this->byDepthProcFormat0 = $procFormat;
        $this->byDepthCompFormat0 = $compProcFormat;
        $this->byDepthPartFormat1 = $partialProcFormat;
    }

    public function formatProcCollection(\ArrayAccess $procedures, int $formatDepth = 0)
    {
        foreach ($procedures as $procedure) {
            switch (get_class($procedure)) {
                case PartialProcedure::class:
                    $func = $this->getByDepthFunc(self::FOR_PARTIAL, $formatDepth);
                    $this->output .= $this->$func($procedure);
                    break;
                case Procedure::class:
                    $func = $this->getByDepthFunc(self::FOR_PROC, $formatDepth);
                    $this->output .= $this->$func($procedure);
                    break;
                case CompositeProcedure::class:
                    $func = $this->getByDepthFunc(self::FOR_COMPOSITE, $formatDepth);
                    $this->output .= $this->$func($procedure);
                    $this->formatProcCollection($procedure->getInners(), $formatDepth + 1);
                    $this->output .= $this->delimiter;
            }
        }
    }

    protected function getByDepthFunc(string $typeFunc, int $depth): string
    {
        $funcName = 'byDepth' . $typeFunc . 'Format' . $depth;
        return $this->$funcName;
    }

    protected function setDelimiter(string $mark)
    {
        $this->delimiter = $mark;
    }

}