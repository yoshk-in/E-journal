<?php


namespace App\GUI\tableStructure;


use App\base\AppMsg;
use App\domain\Product;
use App\events\ISubscriber;
use App\GUI\components\Pager;
use App\GUI\requestHandling\ProductTableSync;
use App\GUI\requestHandling\RowStore;
use App\GUI\helpers\TVisualAggregator;
use App\helpers\AutoGenCollection;
use Psr\Container\ContainerInterface;
use function App\GUI\{offset, size};

class ProductTableMng implements ISubscriber
{
    use TVisualAggregator;

    private ProductTableSync            $tSync;
    protected Table                     $currentTable;
    private ContainerInterface          $container;
    private int                         $productsPerPage;
    private CellRow                     $header;
    private Pager                       $pager;
    private ?Table                      $visibleTable;
    private RowStore                    $store;
    private string                      $product;
    private array                       $events = [];
    private AutoGenCollection           $tableColl;
    private ProductTableFormatter       $formatter;
    private array                       $productTableComponents;

    const EVENTS = [
        AppMsg::GUI_INFO,
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

    protected function initTableProps(): \stdClass
    {
        $tableProps = new \stdClass();
        $tableProps->sizes = size(100, 50);
        $tableProps->offsets = offset(20, 60);
        return $tableProps;
    }

    protected function initTableCollection()
    {
        $tableCollProps = AutoGenCollection::getBlank();
        $tableCollProps->class = Table::class;
        $tableCollProps->scalar = (array)$this->initTableProps();
        $tableCollProps->get = \Closure::fromCallable([$this, 'switchVisibleTableTo']);
        $tableCollProps->make = function (Table $currentTable) {
            $this->pager->addButton(fn($pageNumber) => $this->tableColl->gen($pageNumber - 1));
            $this->switchVisibleTableTo($currentTable);
            $this->currentTable = $currentTable;
        };
        return $tableCollProps;
    }

    public function prepareTable()
    {
        $this->pager->prepare();
        $this->header = $this->formatter->createHeaderRow($this->product, $this->createNewPage());
        $this->productTableComponents = [$this->pager, $this->header, $this->currentTable];
    }

    public function unsetRow(CellRow $row)
    {
        $table = $row->getOwner();
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
        $this->store->add($product->getId(), $row);
    }


    protected function switchVisibleTableTo(Table $table): self
    {
        is_null($this->visibleTable) ?: $this->visibleTable->setVisible(false);
        ($this->visibleTable = $table)->setVisible(true);
        return $this;
    }

    protected function updateTable(Product $product): self
    {
        $this->currentTable->rowCount() < $this->productsPerPage ?: $this->createNewPage();
        $this->createProductRow($product);
        return $this;
    }

    protected function createNewPage(): Table
    {
        return $this->tableColl->gen($this->tableColl->count());
    }


    public function update($product, string $event)
    {
        $this->updateTable($product);
    }

    public function subscribeOn(): array
    {
        $this->events[] = self::EVENTS[0] . $this->product;
        return $this->events;
    }
}