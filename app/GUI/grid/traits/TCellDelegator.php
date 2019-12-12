<?php


namespace App\GUI\grid\traits;


trait TCellDelegator
{

    public function __call($name, $arguments)
    {
        [$getSet, $rest] = $this->destructNameMethod($name);
        switch ($getSet) {
            case 'get':
                return $this->getMethod($rest, $name, $arguments);
            case 'set':
                return $this->setMethod($rest, $name, $arguments);
        }
        return $this->callComponent($name, $arguments);
    }


    protected function destructNameMethod($name): array
    {
        $getSet = substr($name, 0, 3);
        $prop = lcfirst(substr($name, 3));
        return [$getSet, $prop];
    }

    abstract public function getMethod($prop, $name, array $arguments = []);

    abstract public function setMethod($prop, $name, array $arguments);

    public function callComponent($name, $arguments)
    {
        return $this->getComponent()->$name(...$arguments);
    }
}