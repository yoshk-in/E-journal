<?php


namespace App\GUI\tableStructure;


use App\base\AppCmd;
use App\domain\procedures\Product;
use App\events\ISubscriber;
use App\GUI\components\Pager;
use App\GUI\grid\style\RowStyle;
use App\GUI\requestHandling\ProductTableSync;
use App\GUI\requestHandling\RowStore;
use App\GUI\helpers\TVisualAggregator;
use App\helpers\AutoGenCollection;
use Psr\Container\ContainerInterface;
use function App\GUI\cellStyle;

class ProductTableMng implements ISubscriber
{
    use TVisualAggregator;

    private ProductTableSync            $tSync;
    protected Table                     $currentTable;
    private ContainerInterface          $container;
    private int                         $productsPerPage;
    private TableRow                     $header;
    private Pager                       $pager;
    private ?Table                      $visibleTable;
    private RowStore                    $store;
    private string                      $product;
    private array                       $events = [];
    private AutoGenCollection           $tableColl;
    private ProductTableFormatter       $formatter;
    private array                       $productTableComponents;

    const EVENTS = [
        AppCmd::PROCESSING_PRODUCT_AND_NOT_STARTED_INFO,
    ];


    public function __construct(
        ProductTableSync $tSync,
        Pager $pager,
        string $product,
        RowStore $store,
        ContainerInterface $container,
        ProductTableFormatter $formatter)
    {
        $this->tSync = $tSync;
        $this->tSync->attachTableComposer($this);
        $this->container = $container;
        $this->pager = $pager;
        $this->store = $store;
        $this->product = $product;
        $this->productsPerPage = 15;
        $this->tableColl = new AutoGenCollection($container, $this->initTableCollection());
        $this->formatter = $formatter;
        $this->visibleTable = null;
    }



    protected function initTableCollection()
    {
        $tableCollProps = AutoGenCollection::getBlank();
        $tableCollProps->class = Table::class;
        $tableCollProps->scalar = ['style' => cellStyle(new RowStyle(), 100, 50, 20, 60)];
        $tableCollProps->get = fn (Table $switchingTable) => $this->switchVisibleTableTo($switchingTable);
        $tableCollProps->make = function (Table $newTable) {
            $this->pager->addButton(fn($pageNumber) => $this->tableColl->gen($pageNumber - 1));
            $this->switchVisibleTableTo($newTable);
            $this->currentTable = $newTable;
        };
        return $tableCollProps;
    }

    public function prepareTable()
    {
        $this->pager->prepare();
        $this->header = $this->formatter->createHeaderRow($this->product, $this->createNewPage());
        $this->productTableComponents = [$this->pager, $this->header, $this->currentTable];
    }

    public function unsetRow(TableRow $row)
    {
        $table = $row->getParent();
        $table->unsetRow($row->getData()->getNumber());
    }

    protected function getVisualComponents(): array
    {
        return $this->productTableComponents;
    }


    protected function createProductRow(Product $product)
    {
        $row = $this->formatter->createProductRow($product, $this->currentTable);
        //activate cells and sync by proc state
        $this->tSync->activateRowCell($row, $product);
        $this->store->add($product->getProductId(), $row);
    }


    protected function switchVisibleTableTo(Table $table): self
    {
        is_null($this->visibleTable) ?: $this->visibleTable->setVisible(false);
        ($this->visibleTable = $table)->setVisible(true);
        return $this;
    }

    protected function updateTable(Product $product): self
    {
        $this->currentTable->rootRowCount() < $this->productsPerPage ?: $this->createNewPage();
        $this->createProductRow($product);
        return $this;
    }

    protected function createNewPage(): Table
    {
        return $this->tableColl->gen($this->tableColl->count());
    }


    public function notify($product, string $event)
    {
        $this->updateTable($product);
    }

    public function subscribeOn(): array
    {
        $this->events[] = self::EVENTS[0] . $this->product;
        return $this->events;
    }
}