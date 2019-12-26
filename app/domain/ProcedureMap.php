<?php

namespace App\domain;



class ProcedureMap
{
    private $products = [];
    private $procMap;
    const SHORT = true;


    public function __construct(array $procedureMap)
    {
        $this->procMap = $procedureMap;
        foreach ($this->procMap as $product => $procMap) {
            foreach ($procMap as $procProps) {
                $this->products[$product][$procProps['name']] = $procProps;
            }

        }
    }

    public function getProducts(): array
    {
        return array_keys($this->procMap);
    }

    public function getProceduresFor(string $product): array
    {
        $this->inMapProductCheck($product);
        return $this->products[$product];
    }

    public function partialsCount(string $product, string $name): int
    {
        assert($this->isComposite($product, $name), ' non composite procedure ');
        return count($this->procForProduct($product, $name)['inners']);
    }

    public function procForProduct(string $product, string $name)
    {
        $procedures = $this->getProceduresFor($product);
        assert(isset($procedures[$name]), 'в файле конфигурации отсутствует процедура для продукта с данным именем');
        return $procedures[$name];
    }

    public function proceduresForFactory(string $product)
    {
        $this->inMapProductCheck($product);
        return $this->procMap[$product];
    }

    public function getProcedureNames(string $product): array
    {
        foreach ($this->procMap[$product] as $proc) {
            $names[] = $proc['name'];
        }
        return $names;
    }

    public function isComposite(string $product, string $name): bool
    {
        $procedure = $this->procForProduct($product, $name);
        return (isset($procedure['inners']) ? true : false);
    }

    public function getPartials(string $product, string $procedure): array
    {
        $procedures = $this->procMap[$product];
        foreach ($procedures as $key => $proc) {
            $found = ($proc['name'] !== $procedure) ?: $key;
            break;
        }
        return $procedures[$found]['inners'] ?? null;
    }

    public function getPartialNames(string $product, ?string $procedure, ?string $ru): array
    {
        foreach ($this->getPartials($product, $procedure) as $partial) {
            $names[] = $ru ? $partial['short'] : $partial['name'];
        }
        return $names;
    }

    public function getAllPartialNames(string $product, ?string $ru = null) : array
    {
        $names = [];
        foreach ($this->getProceduresFor($product) as $proc) {
            !($proc['inners'] ?? null) OR $names = array_merge($this->getPartialNames(
                $product, $proc['name'], $ru
            ), $names);

        }
        return $names;
    }

    public function getAllDoublePartialNames(string $product) : array
    {
        $shortNames = $this->getAllPartialNames($product, self::SHORT);
        $fullNames = $this->getAllPartialNames($product);
        foreach ($shortNames as $key => $name) {
            $result[] = [$name, $fullNames[$key]];
        }
        return $result;
    }

    public function getAllDoubleProcNames(string $product)
    {
        foreach ($this->getProceduresFor($product) as $proc) {
            $names[] = [$proc['short'], $proc['name']];
        }
        return $names;
    }

    private function inMapProductCheck(string $product)
    {
        assert(isset($this->products[$product]), 'в файле конфигурации отсутствует продукт с данным именем');
    }

}

