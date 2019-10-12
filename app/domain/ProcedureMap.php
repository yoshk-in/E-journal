<?php

namespace App\domain;



class ProcedureMap
{

    private $procMap;
    const SHORT = true;

    public function __construct(array $procedureMap)
    {
        $this->procMap = $procedureMap;
    }

    public function getProducts(): array
    {
        return array_keys($this->procMap);
    }

    public function getProdProcArr(string $product): array
    {
        if (!isset($this->procMap[$product])) throw new \Exception('ошибка чтения файла конфигурации: отсутсвуют процедуры');
        return $this->procMap[$product];
    }

    public function getProcedureNames(string $product): array
    {
        foreach ($this->procMap[$product] as $proc) {
            $names[] = $proc['name'];
        }
        return $names;
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
        foreach ($this->getProdProcArr($product) as $proc) {
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
        foreach ($this->getProdProcArr($product) as $proc) {
            $names[] = [$proc['short'], $proc['name']];
        }
        return $names;
    }

}

