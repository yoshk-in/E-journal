<?php

namespace App\command;

use \App\base\AppHelper;

use \App\base\Request;

abstract class Command
{
    protected $entityManager;

    protected $repo;

    protected $targetClass;

    final public function __construct()
    {
        $this->entityManager = AppHelper::getEntityManager();

    }

    public function execute(Request $request)
    {
        $target = $request->getProperty('targetClass');
        $this->targetClass = $this->getTargetClass($target);
        $this->repo = $this->entityManager->getRepository($this->targetClass);
        $this->doExecute($request);

        echo static::class ."\n";

    }

    protected function getTargetClass(string $name)
    {
        return '\App\domain\\'.$name;
    }

    abstract protected function doExecute(Request $request);

}

