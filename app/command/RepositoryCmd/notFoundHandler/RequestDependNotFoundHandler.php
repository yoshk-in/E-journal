<?php


namespace App\command\RepositoryCmd\notFoundHandler;


use App\base\AbstractRequest;
use App\command\RepositoryCmd\RepositoryCmd;
use App\command\RepositoryCmd\repositoryRequest\CreateRequest;
use App\repository\ProductRepository;
use Generator;
use Psr\Container\ContainerInterface;

class RequestDependNotFoundHandler extends NotFoundHandler
{
    protected AbstractRequest $request;
    protected NotFoundHandler $delegateTo;
    protected CreateNotFoundsHandler $createNotFounds;
    protected CommonNotFoundHandler $notFoundHandler;

    public function __construct(AbstractRequest $request, ProductRepository $repository)
    {
        $this->request = $request;
        $this->notFoundHandler = new CommonNotFoundHandler();
        $this->createNotFounds = new CreateNotFoundsHandler(new CreateRequest($repository), $this->notFoundHandler);
    }

    public function repeatExecuting($request): bool
    {
        $this->delegateTo = $this->request->AreCreateNotFounds() ? $this->createNotFounds : $this->notFoundHandler;
        return $this->delegateTo->repeatExecuting($request);
    }

    public function getExecutors(): array
    {
        return $this->delegateTo->getExecutors();
    }
}