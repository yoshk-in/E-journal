<?php


namespace App\domain;


class ProductMap
{
    private $map;
    private $err = 'ошибка файла конфигурации: не удается прочитать свойства продукта';

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function getProducts(): array
    {
        assert(!empty($this->map), $this->err);
        return $this->map;
    }

    public function getProductProps(string $product)
    {
        return $this->map[$product];
    }

    public function isCountable(string $product): bool
    {
        return $this->getProductProps($product)['monthly countable'] ?? false;
    }

    public function getNumberStrategy(string $product): string
    {
        return $this->getProductProps($product)['numberStrategy'];
    }

    public function getCountableProducts(): \Iterator
    {
        foreach ($this->map as $product => $props) {
            if ($this->isCountable($product)) yield $product;
        }
    }

    public function first(): string
    {
        assert(!empty($this->map), $this->err);
        return array_key_first($this->map);
    }

    public function getMainNumberLength(string $product): ?int
    {
        $this->isRightName($product);
        return $this->map[$product]['mainNumberLength'] ?? null;
    }

    private function isRightName(string $product)
    {
        assert(isset($this->map[$product]), ' в файле конфигигурации нет продукта с таким именем');
    }

}