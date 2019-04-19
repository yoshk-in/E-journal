<?php

namespace App\command;

use \App\base\AppHelper;

use \App\base\Request;

abstract

class Command
{
    protected $entityManager;

    protected $repo;

    final public function __construct()
    {
        $this->entityManager = AppHelper::getEntityManager();

        $this->repo = $this->entityManager->getRepository('\App\domain\G9');

    }

    public function execute(Request $request)
    {
        $this->doExecute($request);

        echo static::class ."\n";

    }

    abstract protected function doExecute(Request $request);

}

