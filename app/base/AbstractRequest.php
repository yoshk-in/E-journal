<?php


namespace App\base;


use App\base\exceptions\WrongInputException;
use App\domain\data\ProductData;

abstract class AbstractRequest
{
    /** @var ProductData */
    const PRODUCT_DATA = ProductData::class;
    const ERR_NUMBER_SEQUENCE = 'диапазон номеров должен задаваться по возврастающей, переданы номера: %s > %s';
    const ERR_REPEAT = 'переданы повторяющиеся номера, номер %s передан дважды';
    
    /** @var ProductData[] */
    protected array $productData = [];
    protected array $commands = [];
    protected array $productNumbers = [];
    protected ?string $partialProcName = null;
    protected array $doubleNumbers = [];
    protected array $changingNumbers = [];
    protected array $advancedNumbers = [];
    protected ?bool $createNotFoundProducts = false;

    public function addProductData(string $number)
    {
        $data = new ProductData($number);
        $id = $data->getId();
        if (isset($this->productData[$id])) WrongInputException::create(self::ERR_REPEAT, [$id]);
        $this->productData[$id] = $data;
    }

    public function addProductDataRange(int $from, int $to)
    {
        if ($from > $to) WrongInputException::create(self::ERR_NUMBER_SEQUENCE,[$from, $to]);
        $numbers = range($from, $to);
        foreach ($numbers as $number) {
            $this->addProductData($number);
        }
    }

    /**
     * @return ProductData[]
     */
    public function getRequestingData(): array
    {
        return $this->productData;
    }

    public function removeData($id)
    {
        unset($this->productData[$id]);
    }



    public function addChangingNumber($advancedNumber, $mainNumber)
    {
        $this->changingNumbers[$advancedNumber] = $mainNumber;
    }

    public function setAdvancedNumbers(array $advancedNumbers)
    {
        $this->advancedNumbers = $advancedNumbers;
    }

    public function getChangingNumbers(): array
    {
        return $this->changingNumbers;
    }


    public function getProductName(): string
    {
        return (self::PRODUCT_DATA)::getName();
    }


    public function prepareProductRequest(string &$productName): void
    {
        (self::PRODUCT_DATA)::setProductName($productName);
    }

    public function getCmd(): array
    {
        return $this->commands;
    }


    public function addCmd(string $command): void
    {
        $this->commands[$command] = $command;
    }


    public function getProductNumbers(): array
    {
        return $this->productNumbers;
    }

    public function getBlockDoubleNumbers(): array
    {
        return $this->doubleNumbers;
    }


    public function setProductNumbers(?array $productNumbers): void
    {
        $this->productNumbers = $productNumbers;
    }



    public function setParty($partNumber): void
    {
        $this->partNumber = $partNumber;
    }


    public function getPartial(): ?string
    {
        return $this->partialProcName;
    }


    public function setPartial(?string $partialProcName): void
    {
        $this->partialProcName = $partialProcName;
    }


    public function getAdvancedNumbers(): array
    {
        return $this->advancedNumbers;
    }

    public function createNotFoundProducts(bool $bool)
    {
        $this->createNotFoundProducts = $bool;
    }

    public function AreCreateNotFounds(): bool
    {
        $bool = $this->createNotFoundProducts;
        $this->createNotFoundProducts = false;
        return $bool;
    }


    public function getProps(): array
    {
        return [$this->getRequestingData(), $this->getPartial()];
    }

}