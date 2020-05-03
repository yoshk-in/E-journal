<?php


namespace App\repository;


class RequestBuffer
{
    protected ?array $order;
    protected array $where = [];



    public function __invoke(): array
    {
        return $this->getBuffer();
    }

    public function desc(string $field): self
    {
        $this->order = [$field => 'DESC'];
        return $this;
    }

    public function asc(string $field): self
    {
        $this->order = [$field => 'ASC'];
        return $this;
    }

    public function where(array $conditions): self
    {
        $this->where = array_merge($this->where, $conditions);
        return $this;
    }

    public function reset(): self
    {
        $this->order = null;
        $this->where = [];
        return $this;
    }

    public function getBuffer(): array
    {
        return [$this->where, $this->order];
    }
}