<?php


namespace App\helpers;


use Psr\Container\ContainerInterface;


class AutoGenCollection
{

    private array $store = [];

    private ContainerInterface $container;
    private GeneratingProps $staticProps;
    private GeneratingProps $dynamicProps;
    const CLASS_REQUIRED_ERR = ' class collection required';


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
        $this->dynamicProps = $dynamicProps ?? self::getBlank();
        if (!$class = $dynamicProps->class ?? $this->staticProps->class) throw new \Exception(self::CLASS_REQUIRED_ERR);

        [$target, $afterGenCall] = isset($this->store[$class][$key]) ?
            [$this->store[$class][$key], $this->staticProps->get]
            :
            [$this->generateToCollection($class, $key), $this->staticProps->make];

        is_null($afterGenCall) ?: $afterGenCall($target);

        return $target;
    }

    public function count(): int
    {
        return count($this->store);
    }

    protected function generateToCollection(string $class, $key)
    {
        $injects = $this->createInjections(array_merge($this->staticProps->inject, $this->dynamicProps->inject));
        $scalars = array_merge($this->staticProps->scalar, $this->dynamicProps->scalar);
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