<?php

namespace App\domain;



use SebastianBergmann\FileIterator\Iterator;

/**
 * Class ProcedureMap
 * @package App\domain
 * @method  Iterator getAllDoublePartialNames(string $product)
 * @method Iterator  getAllProductCompositeProc(string $product)
 * @method Iterator getAllDoubleProcNames(string $product)
 * @method Iterator getProcedureNames(string $product)
 */
class ProcedureMap
{
    protected array $products = [];
    protected array $procMap = [];
    private \stdClass $objectMap;
    protected array $cache = [];



    public function __construct(array $procedureMap)
    {
        $this->procMap = $procedureMap;
        $this->objectMap = (object) $procedureMap;
        $this->products = $this->procMap;
    }

    public function __call($name, $arguments): \Iterator
    {
        if (!method_exists($this, substr($name, 3))) throw new \Exception('undefined method');
        if ($cache = $this->getCache($product = $arguments[0], 'get' . $name )) return $cache;
        while ($res = $this->$name(...$arguments)) {
            yield $this->setCache($product, $name, $res);
        }
    }

    public function getProducts(): array
    {
        return $this->products;
    }


    public function getProcedures(string $product): array
    {
        $this->inMapProductCheck($product);
        return $this->products[$product];
    }


    public function partialsCount(string $product, string $name): int
    {
        return count($this->procForProduct($product, $name)['inners']) ?? 0;
    }

    public function procForProduct(string $product, string $name)
    {
        return  $this->getProcedures($product)[$name] ?? false;
    }


    protected function ProcedureNames(string $product): \Generator
    {
        foreach ($this->procMap[$product] as $proc) {
            yield $proc;
        }
    }

    public function isComposite(string $product, string $name): bool
    {
        $procedure = $this->procForProduct($product, $name);
        return isset($procedure['inners']);
    }


    protected function AllProductCompositeProc(string $product): \Generator
    {
        foreach ($this->procMap[$product] as $proc => $properties) {
            if (!($comps = $properties['inners'] ?? null)) continue;
            yield $comps;
        }
    }

    protected function AllDoublePartialNames(string $product): \Generator
    {
        foreach ($this->getAllProductCompositeProc($product) as $procedure => $property)
        {
            if (!($inners = $property['inners'] ?? null)) continue;
            foreach ($inners as $inner_name => $inner_properties) {
                yield [$inner_properties['short'], $inner_name];
            }
        }
    }

    protected function AllDoubleProcNames(string $product): \Generator
    {
        foreach ($this->getProcedures($product) as $proc_name => $proc) {
            yield [$proc['short'], $proc_name];
        }
    }

    private function inMapProductCheck(string $product)
    {
        assert(isset($this->procMap[$product]), 'в файле конфигурации отсутствует продукт с данным именем');
    }


    protected function getCache(string $product, string $functionName)
    {
        return $this->cache[$product][$functionName] ?? null;
    }

    protected function setCache(string $product, string $functionName, $value)
    {
        return $this->cache[$product][$functionName][] = $value;
    }


}

