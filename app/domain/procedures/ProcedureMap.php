<?php

namespace App\domain\procedures;


use Generator;
use stdClass;
use \Iterator;
use \Closure;


class ProcedureMap
{
    protected array $products = [];
    protected array $procMap = [];
    private stdClass $objectMap;
    protected array $cache = [];
    protected array $currentProduct;
    private string $currentProductName;


    public function __construct(array $procedureMap)
    {
        $this->procMap = $procedureMap;
        $this->objectMap = (object)$procedureMap;
        $this->products = $this->procMap;

    }

    public function setServicedProduct(string $name)
    {
        $this->currentProductName = $name;
        $this->currentProduct = $this->products[$name];
    }



    protected function getOverCaching(string $name, Closure $closure): Iterator
    {
        if (($cache = $this->getCache($name, $this->currentProductName)) !== false) return $cache;
        yield from $this->cachingGetter($name, $closure);
    }


    protected function cachingGetter($name,Closure $closure): Generator
    {
        $toCache = [];
        foreach ($closure() as $key => $resultItem) {
            yield $toCache[$key] = $resultItem;
        }
        $this->setCache($this->currentProductName, $name, $toCache);
    }


    public function getNextProductState(string $name): string
    {
        return $this->currentProduct[$name]['next'];
    }

    public function isValidProduct(string $name): bool
    {
        return in_array($name, array_keys($this->products));
    }

    public function getPartials(string $composite)
    {
        return $this->currentProduct[$composite]['inners'];
    }


    public function getProcedures(): array
    {
        return $this->currentProduct;
    }


    public function getPartialsCountByComposite(string $name): int
    {
        return count($this->currentProduct[$name]['inners']) ?? 0;
    }


    public function getProcedureNames(): Generator
    {
        foreach ($this->currentProduct as $proc) {
            yield $proc;
        }
    }


    public function isComposite(string $name): bool
    {
        return isset($this->currentProduct[$name]['inners']);
    }

    // caching
    public function getAllComposites(): Iterator
    {
        return $this->getOverCaching(__FUNCTION__, fn(): Generator => $this->findAllComposites());
    }

    protected function findAllComposites(): Generator
    {
        foreach ($this->currentProduct as $proc => $properties) {
            if (!isset($properties['inners'])) continue;
            yield $proc => $properties;
        }
    }

    // caching
    public function getAllPartialNamesWithAliases(): Iterator
    {
        return $this->getOverCaching(__FUNCTION__, fn (): Generator => $this->findAllPartialNamesWithAliases());

    }

    protected function findAllPartialNamesWithAliases(): Generator
    {
        foreach ($this->getAllComposites() as $compositeProperties) {
            foreach ($compositeProperties['inners'] as $innerName => $innerProps) {
                yield [$innerProps['short'], $innerName];
            }
        }
    }


    protected function getCache(string $functionName, $product)
    {
        return $this->cache[$functionName][$product] ?? false;
    }

    protected function setCache(string $product, string $functionName, $value)
    {
        return $this->cache[$functionName][$product] = $value;
    }


}

