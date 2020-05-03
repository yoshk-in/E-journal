<?php


namespace App\command\RepositoryCmd\repositoryRequest;


use App\base\AbstractRequest;
use App\domain\data\AbstractProductData;
use App\domain\data\ProductData;
use App\repository\ProductRepository;
use Generator;

abstract class RepositoryRequest
{
    protected ProductRepository $productRepository;
    /** @var string | AbstractProductData */
    const REQUESTING_SUBJECT_DATA = AbstractProductData::class;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }


    /**
     * @param ProductData[] $productData
     * @return Generator
     */
    abstract public function doGet(array $productData): Generator;



    public function get(AbstractRequest $request): Generator
    {
        foreach ($this->doGet($request->getRequestingData()) as $found) {
            $this->removeFoundData($request, $found);
            yield $found->getProductId() => $found;
        }
    }

    public function __invoke(AbstractRequest $request): Generator
    {
        return $this->get($request);
    }


    protected function removeFoundData(AbstractRequest $request, $found)
    {
        // @TODO: implement request method get dataKey (may be delegate it to productData entity by 'findBy' method?)...
        // @TODO: ...which reflects keys in request product data array and remove by it
        $request->removeData($found->getProductId());
    }

}