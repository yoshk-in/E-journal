<?php


namespace App\command\RepositoryCmd\notFoundHandler;


use App\base\AbstractRequest;
use App\command\RepositoryCmd\foundHandler\FoundHandler;
use App\command\RepositoryCmd\RepositoryCmd;
use App\command\RepositoryCmd\repositoryRequest\CreateRequest;
use Generator;

class CreateNotFoundsHandler extends NotFoundHandler
{
    protected CreateRequest $creatRequest;
    protected ?FoundHandler $foundHandler;
    protected CommonNotFoundHandler $notFoundHandler;

    protected array $executorsStack = [];

    public function __construct(CreateRequest $createRequest, CommonNotFoundHandler $notFoundHandler)
    {
        $this->executorsStack[] = $this->creatRequest = $createRequest;
        $this->executorsStack[] = $this->foundHandler = null;
        $this->executorsStack[] = $this->notFoundHandler = $notFoundHandler;
    }

    public function repeatExecuting(AbstractRequest $request): bool
    {
        return empty($request->getRequestingData()) ? false : true;
    }

    public function getExecutors(): array
    {
        return $this->executorsStack;
    }
}