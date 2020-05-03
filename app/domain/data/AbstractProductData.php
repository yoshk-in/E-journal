<?php


namespace App\domain\data;


use App\base\exceptions\WrongInputException;
use App\cache\Cache;
use App\domain\numberStrategy\NumberStrategy;
use App\domain\procedures\factories\IProductProcedureFactory;
use App\domain\procedures\factories\ProcedureFactory;
use App\domain\AbstractProduct;
use App\events\Event;
use App\events\traits\TObservable;
use Psr\Container\ContainerInterface;

abstract class AbstractProductData
{
    use TObservable;

    /** @var string|ProductIdTransformer */
    const ID_GENERATOR = ProductIdTransformer::class;
    protected string $id;

    const PRODUCT_PROPERTIES_MAP = [
        'id' => 'id',
        'state' => 'state',

    ];

    protected static ?string $servicedProductClass;
    protected static string $productName;
    protected static array $commonRequestingProductProps = [];
    protected static string $findingBy = self::PRODUCT_PROPERTIES_MAP['id'];
    protected static string $orderBy = self::PRODUCT_PROPERTIES_MAP['id'];

    /** @var string|NumberStrategy|null */
    protected static ?string $numberStrategy;
    protected static IProductProcedureFactory $procedureFactory;
    protected static Cache $partNumberSource;

    protected static int $fullNLength;
    protected static int $shortNLength;
    protected static int $partNLength;


    public static function changeNumberAndId(AbstractProduct $product, int $newNumber): array
    {
        $numberStrategy = $product->getNumberStrategy();
        $productName = $product->getProductName();
        $prevNumber = $product->getProductNumber();
        [$newNumber, $preId] = $numberStrategy::changeMainNumber($prevNumber, $newNumber);
        return [$newNumber, (self::ID_GENERATOR)::getId($productName, $preId)];
    }

    public static function setProductName(string $productName)
    {
        self::$productName = self::normalizeProductName($productName);
        Event::create(self::class, Event::PRODUCT_HAS_BEEN_SET);
    }

    protected static function normalizeProductName(string $name): string
    {
        return mb_strtoupper($name);
    }


    public static function setProductClass(string $class)
    {
        self::$servicedProductClass = $class;
    }

    public static function setProcedureFactory(ProcedureFactory $factory)
    {
        self::$procedureFactory = $factory;
    }

    public static function setPartNumberSource(Cache $cache)
    {
        self::$partNumberSource = $cache;
    }

    public static function setNumberLengths(int $full, int $part, int $short)
    {
        self::$fullNLength = $full;
        self::$partNLength = $part;
        self::$shortNLength = $short;
    }


    public static function setNumberStrategy(string $strategy)
    {
        self::$numberStrategy = $strategy;
    }


    public static function getName(): string
    {
        return self::$productName;
    }


    public static function findNotEnded()
    {
        self::$commonRequestingProductProps[self::PRODUCT_PROPERTIES_MAP['state']] = AbstractProduct::STARTED;
    }

    public static function findBy(): string
    {
        return self::$findingBy;
    }

    public static function orderBy(): string
    {
        return self::$orderBy;
    }


    public static function resetFinding()
    {
        self::$commonRequestingProductProps = [];
    }

    public static function getServicedClass(): string
    {
        return self::$servicedProductClass;
    }


    public static function getCommonRequestingProductProps(): array
    {
        return self::$commonRequestingProductProps;
    }



    abstract function getNumber();

    abstract function getPreNumber();

    abstract function getId();

    abstract static function resetCreating();
}