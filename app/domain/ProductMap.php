<?php
declare(strict_types=1);

namespace App\domain;


use App\base\exceptions\WrongInputException;
use Iterator;
use App\domain\numberStrategy\DoubleNumberStrategy;

class ProductMap
{
    private array $map;
    private array $servicedProductMap;
    const ERROR = 'ошибка файла конфигурации: не удается прочитать свойства продукта';

    public function __construct(array $map)
    {
        $this->map = $map;
        assert(!empty($this->map), self::ERROR);
    }

    public function getProducts(): array
    {
        return $this->map;
    }

    public function setServicedProduct(string $productName)
    {
        $this->servicedProductMap = $this->map[$productName];
    }

    public function getProductNames(): \Generator
    {
        foreach ($this->map as $productName => $props) {
            yield $productName;
        }
    }


    public function getProductProps()
    {
        return $this->servicedProductMap;
    }

    public function isCountable(string $productName): bool
    {
        return $this->map[$productName]['monthly countable'];
    }

    public function isDoubleNumbering(): bool
    {
        return DoubleNumberStrategy::class === $this->getNumberStrategy();
    }

    public function getNumberStrategy(): string
    {
        return $this->servicedProductMap['numberStrategy'];
    }


    public function firstProductName(): string
    {
        return array_key_first($this->map);
    }

    public function getMainNumberLength(): int
    {
        return $this->servicedProductMap['mainNumber.length'];
    }

    public function getPartNumberLength(): ?int
    {
        return $this->servicedProductMap['partNumber.length'];
    }

    public function getPreNumberLength(): ?int
    {
        return $this->servicedProductMap['partNumber.length'];
    }


}