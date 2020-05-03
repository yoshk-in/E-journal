<?php


namespace App\domain\data;


use App\base\exceptions\WrongInputException;
use App\cache\Cache;
use App\domain\numberStrategy\NumberStrategy;
use App\domain\procedures\data\AbstractProcedureData;
use App\domain\procedures\factories\IProductProcedureFactory;
use App\domain\procedures\factories\ProcedureFactory;
use App\domain\procedures\decorators\ProductOwnerDecorator;
use App\domain\AbstractProduct;
use App\domain\ProductMap;
use Psr\Container\ContainerInterface;

class ProductData extends AbstractProductData
{
    private int $mainNumber;
    private int $preNumber;


    /** @var string|AbstractProcedureData */
    const PROCEDURE_DATA = AbstractProcedureData::class;
    const PROCEDURE_DATA_OWNER_STRATEGY = ProductOwnerDecorator::class;

    const WRONG_NUMBER_LENGTH = ' номера блоков должны задаваться %s цифрами';
    const OR = ' или %s ';

    public function __construct(string $number)
    {
        $number = self::getFullNumber($number);
        [$this->mainNumber, $this->preNumber, $preId] = (self::$numberStrategy)::initProductNumbers($number);
        $this->id = (self::ID_GENERATOR)::getId(self::getName(), $preId);
    }


    public function createProcedures(AbstractProduct $product): \Generator
    {
        (self::PROCEDURE_DATA)::setOwnerData($product, self::PROCEDURE_DATA_OWNER_STRATEGY);
        (self::PROCEDURE_DATA)::resetProductCounter();
        $factory = self::$procedureFactory;
        yield from $factory->create($product);
    }



    public static function getNumberStrategy(): string
    {
        return self::$numberStrategy;
    }

    public static function getFullNumbers(array $numbers): \Generator
    {
        foreach ($numbers as $number) {
            yield self::getFullNumber($number);
        }
    }

    public static function getFullNumber(string $number): int
    {
        switch (strlen($number)) {
            case self::$fullNLength:
                break;
            case self::$shortNLength:
                $partNumber = (self::$partNumberSource)->getPartNumber(self::getName());
                if (!$partNumber) throw new WrongInputException((self::$partNumberSource)::PART_NUMBER_ERROR);
                $number = $partNumber . $number;
                break;
            default:
                $addToMsg = self::$fullNLength;
                $addToMsg = (!self::$shortNLength) ?: $addToMsg . sprintf(self::OR,  self::$shortNLength);
                throw new WrongInputException(sprintf(self::WRONG_NUMBER_LENGTH, $addToMsg));
            }
            return $number;
    }

    public function getData(): array
    {
        return [$this->getNumber(), $this->preNumber, $this->getId(), self::getNumberStrategy()];
    }

    public function __toString()
    {
        return $this->id;
    }

    function getNumber(): int
    {
        return $this->mainNumber;
    }

    function getPreNumber(): int
    {
        return $this->preNumber;
    }

    function getId(): string
    {
        return $this->id;
    }

    public function createProduct(): AbstractProduct
    {
        return new self::$servicedProductClass($this);
    }


    static function resetCreating()
    {
        self::$fullNLength = -1;
        self::$shortNLength = -1;
        self::$servicedProductClass = null;
        self::$numberStrategy = null;
    }
}