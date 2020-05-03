<?php


namespace App\command\RepositoryCmd\notFoundHandler;


use App\base\AbstractRequest;

abstract class NotFoundHandler
{
    abstract public function repeatExecuting(AbstractRequest $requestToCheckNotFounds): bool;

    abstract public function getExecutors(): array;

}