<?php


namespace App\helpers;


use Psr\Container\ContainerInterface;


class AutoGenCollection
{

    private array $store = [];

    private ContainerInterface $container;
    private GeneratingProps $staticProps;
    private ?GeneratingProps $dynamicProps;


    public function __construct(ContainerInterface $container, GeneratingProps $genProps = null)
    {
        $this->container = $container;
        $this->staticProps = $genProps ?? self::getBlank();
    }

    public static function getBlank(): GeneratingProps
    {
        return new GeneratingProps();
    }

    public function gen($key, ?GeneratingProps $dynamicProps = null)
    {
        $this->dynamicProps = $dynamicProps;

        $class = $dynamicProps->class ?? $this->staticProps->class;
        assert(!is_null($class), ' class collection required');

        if (isset($this->store[$class][$key])) {
            $target = $this->getFromCollection($class, $key);
            $afterClosure = $this->staticProps->get ?? null;
        } else {
            $target = $this->addGeneratingToCollection($class, $key);
            $afterClosure = $this->staticProps->make ?? null;
        }

        is_callable($afterClosure) ? $afterClosure($target) :
            (is_null($afterClosure) ?: assert(false, "expected closure " . gettype($afterClosure) . " got" ) );

        return $target;
    }

    public function count(): int
    {
        return count($this->store);
    }

    protected function getFromCollection($class, $key)
    {
        return $this->store[$class][$key];
    }

    protected function addGeneratingToCollection(string $class, $key)
    {
        $injects = $this->createInjections(array_merge($this->staticProps->inject ?? [], $this->dynamicProps->inject ?? []));
        $scalars = array_merge($this->staticProps->scalar ?? [], $this->dynamicProps->scalar ?? []);
        $createParameters = array_merge($scalars, $injects);
        return $this->store[$class][$key] = $this->container->make($class, $createParameters);
    }

    protected function createInjections(array $inject): array
    {
        foreach ($inject as $propName => $injection) {
            $injections[$propName] = $this->container->make($injection);
        }
        return $injections ?? [];
    }


}