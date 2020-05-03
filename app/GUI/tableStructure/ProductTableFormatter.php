<?php


namespace App\GUI\tableStructure;


use App\domain\procedures\CasualProcedure;
use App\domain\procedures\CompositeProcedure;
use App\domain\procedures\ProcedureMap;
use App\domain\procedures\Product;
use App\domain\procedures\ProductMap;
use App\GUI\grid\style\Style;
use App\GUI\ProductStateColorant;
use Closure;
use Doctrine\Common\Collections\Collection;
use function App\GUI\colorStyle;
use function App\GUI\textStyle;

class ProductTableFormatter
{
    protected ProductStateColorant $colorize;
    private Table $handledTable;
    private ProcedureMap $procedureMap;
    private string $handledProduct;
    private Style $cellStyle;
    private Style $compositeStyle;
    private Style $textCellStyle;
    private Style $inputCellStyle;
    public Closure $createCasualProcCell;
    public Closure $createPartialProcCell;
    protected array $procStyleStack = [];
    private Closure $createCell;

    public function __construct(ProductStateColorant $colorize, ProcedureMap $procedureMap, CellStyleInitializer $stylist)
    {
        $this->procedureMap = $procedureMap;
        $this->procStyleStack[] = $this->cellStyle = $stylist->getCommonCellStyle();
        $this->procStyleStack[] = $this->compositeStyle = $stylist->getCompositeCellStyle();
        $this->procStyleStack[] = $this->textCellStyle = $stylist->getTextCellStyle();
        $this->inputCellStyle = $stylist->getInputCellStyle();
        $this->procCellCreatorsInit($colorize);
    }

    protected function procCellCreatorsInit(ProductStateColorant $color)
    {
        $this->createCell = fn($proc, $cellStyle) => $this->table()->addCell($color->style($cellStyle, $proc));
        $this->createCasualProcCell =
            fn(CasualProcedure $proc, $cellStyle, $compStyle, $partStyle) => ($proc->isComposite()) ?
                $this->compositeCell($proc, $partStyle, $compStyle)
                :
                ($this->createCell)($proc, $cellStyle);

        $this->createPartialProcCell = fn(CasualProcedure $proc, $cellStyle) => $this->table()->addCell(
            $color->style(textStyle($cellStyle, $proc->getName()), $proc)
        );
    }


    public function createHeaderRow(string $product, Table $table): TableRow
    {
        $this->handledTable = $table;
        $this->handledProduct = $product;
        /** @var object $map */
        $map = new class() {
            public ProcedureMap $procMap;
            public string $product;

            public function __call($name, $arguments) {
                $this->procMap->$name($this->product, ...$arguments);
            }
        };
        $map->procMap = $this->procedureMap;
        $map->product = $this->handledProduct;
        //xy header - first cell
        $header = $this->table()->getCurrentRow();
        $this->table()->addCell(textStyle(clone $this->cellStyle, 'номера',));

        //rest headers
        $headerStyle = clone $this->textCellStyle;
        $headerStyle->on = [];
        $compositeHeaderStyle = clone $headerStyle;

        foreach ($map->getProcedures() as $proc) {
            if ($map->isComposite($proc)) {
                $compositeHeaderStyle->width =
                    $this->compositeStyle->byDefer('width', $map->partialsCount($proc), $headerStyle->width);
                $cellStyle = $compositeHeaderStyle;
            } else $cellStyle = $headerStyle;
            $this->table()->addCell(textStyle($cellStyle, $proc));
        }
        return $header;
    }

    public function createProductRow(Product $product, Table $table): TableRow
    {
        $this->handledTable = $table;
        $row = $table->newDataRow($product->getProductId(), $product);
        //product number cell
        $cellStyle = ($text = $product->getNumber()) ? textStyle($this->textCellStyle, $text) : $this->inputCellStyle;
        $this->table()->addCell($cellStyle);
        //rest cells
        $this->createProcedureRow($this->createCasualProcCell, $product->getProcedures(), $this->procStyleStack);
        return $row;
    }



    protected function createProcedureRow(Closure $createCell, Collection $procedures, array $cellStyleStack)
    {
        array_map(fn(CasualProcedure $proc) => $createCell($proc->getName(), ...$cellStyleStack), $procedures->toArray());
    }


    protected function compositeCell(CompositeProcedure $procedure, Style $textStyle, Style $compStyle)
    {
        $parts = $procedure->getInnerProcedures();
        $compStyle->width = $this->compositeStyle->byDefer('width', $parts->count(), $textStyle->width);
        $textStyle = clone $textStyle;
        $textStyle->height = $compStyle->height - 2 * $compStyle->padding;
        $textStyle->width -= 2 * $compStyle->padding;
        return ($this->createCell)($procedure, $compStyle)
            //create inner cells
            ->nestInCellNewRow(
                fn() => $this->createProcedureRow($this->createPartialProcCell, $parts, [$textStyle]),
                $this->table()->getRow($this->table()->rootRowCount() - 1, 0)
            );
    }

    protected function table(): Table
    {
        return $this->handledTable;
    }


}