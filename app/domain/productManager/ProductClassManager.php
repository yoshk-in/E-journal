<?php


namespace App\domain\productManager;


use App\base\exceptions\ObjectStateException;
use App\domain\AbstractProduct;
use App\domain\productClassGen\Product0;
use App\domain\ProductMap;
use App\events\Event;
use App\events\IEventType;
use App\events\traits\TObservable;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use Nette\Utils\FileSystem;
use PhpParser\Node\Expr\Variable;

/**
 * Class ProductClassManager
 * @package App\domain\productGenerator
 */
class ProductClassManager
{
    const ERR_GEN = 'ошибка сгенерированных классов продуктов';

    const NAMESPACE_SEPARATOR = '\\';
    const ABSTRACT_PRODUCT_NAMESPACE = 'App\\domain\\';
    const ABSTRACT_PRODUCT_CLASS = AbstractProduct::class;
    const ABSTRACT_PRODUCT_DIR = 'app/domain/';
    const PRODUCT_NAME_GEN_PATTERN = 'Product%s';
    const PRODUCT_GEN_FOLDER = 'productClassGen';
    const PRODUCT_GEN_DIR = self::ABSTRACT_PRODUCT_DIR . self::PRODUCT_GEN_FOLDER . DIRECTORY_SEPARATOR;
    const PRODUCT_GEN_NAMESPACE = self::ABSTRACT_PRODUCT_NAMESPACE . self::PRODUCT_GEN_FOLDER . self::NAMESPACE_SEPARATOR;
    const PRODUCT_GEN_PATTERN = self::PRODUCT_GEN_NAMESPACE . self::PRODUCT_NAME_GEN_PATTERN;
    const EVENT_SYS_TRAIT = TObservable::class;
    const EVENT_INTERFACE = IEventType::class;


    private ProductMap $productMap;
    protected array $productClassBuffer = [];
    /** @var string | Product0 */
    protected string $servicedClass;
    protected ?PsrPrinter $printer = null;


    public function __construct(ProductMap $productMap, bool $checkEnvConf = true)
    {
        $this->productMap = $productMap;
        !$checkEnvConf ?: $this->checkComparingEnvToConfig();
    }


    public function getProductNumberProperties(): array
    {
        return [
            $this->productMap->getMainNumberLength(),
            $this->productMap->getPartNumberLength(),
            $this->productMap->getPreNumberLength()
        ];
    }

    public function getServicedProductClass(string $name): string
    {
        $name = mb_strtoupper($name);
        $this->servicedClass = $this->productClassBuffer[$name] ?? ObjectStateException::create(self::ERR_GEN);
        return $this->servicedClass;
    }

    public function getNumberStrategy(): string
    {
        return $this->productMap->getNumberStrategy();
    }

    public function getCountableProducts(): \Generator
    {
        foreach ($this->productClassBuffer as $productClass) {
            !$this->productMap->isCountable($productClass::NAME) ?: yield $productClass;
        }
    }

    public function generateProductClasses()
    {
        $generatingProductNamespace =
            ProductClassManager::ABSTRACT_PRODUCT_NAMESPACE . ProductClassManager::PRODUCT_GEN_FOLDER;

        if (is_null($this->printer)) $this->printer = new \Nette\PhpGenerator\PsrPrinter();

        foreach ($this->productMap->getProductNames() as $id => $productName) {
            $file = new PhpFile();
            $namespace = $file->addNamespace($generatingProductNamespace)->addUse(self::EVENT_INTERFACE);
            $className = sprintf(ProductClassManager::PRODUCT_NAME_GEN_PATTERN, $id);

            $class = $namespace->addClass($className);

            $parameter = (new Parameter('event'))->setDefaultValue(new Literal('IEventType::ANY'));

            $class
                ->addComment('@Entity')
                ->setExtends(ProductClassManager::ABSTRACT_PRODUCT_CLASS)
                ->addTrait(ProductClassManager::EVENT_SYS_TRAIT)
                ->setConstants(['NAME' => $productName])
                ->addMethod('event')
                ->setParameters([$parameter])
                ->setBody('$this->bubbleUpEvent((string)$event );');

            $fullFilePath = ProductClassManager::PRODUCT_GEN_DIR . $className . '.php';
            FileSystem::write($fullFilePath, $this->printer->printFile($file));
        }
        $this->checkComparingEnvToConfig();
    }


    /**
     * @throws ObjectStateException
     */
    protected function checkComparingEnvToConfig()
    {
        foreach ($this->productMap->getProductNames() as $productClassId => $name) {
            if (!class_exists($class = sprintf(self::PRODUCT_GEN_PATTERN, $productClassId)))
                ObjectStateException::create(self::ERR_GEN);
            /** @var Product0 $class */
            if ($class::NAME !== $name)
                ObjectStateException::create(self::ERR_GEN);
            $this->productClassBuffer[$name] = $class;
        }
    }


    public function __call($name, $arguments)
    {
        $this->productMap->$name(...$arguments);
    }
}