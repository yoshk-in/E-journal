<?php


namespace App\command\RepositoryCmd;


use App\base\AbstractRequest;
use App\command\AbstractRepositoryCommand;
use App\domain\AbstractProduct;
use App\helpers\Gen;
use App\repository\ProductRepository;
use App\command\RepositoryCmd\{foundHandler\FoundHandler, notFoundHandler\NotFoundHandler, repositoryRequest\RepositoryRequest};
use Generator;

class RepositoryCmd extends AbstractRepositoryCommand
{
    protected FoundHandler $foundHandler;
    protected RepositoryRequest $requester;
    protected NotFoundHandler $notFoundHandler;
    protected \Closure $executor;

    public function __construct(ProductRepository $repository, AbstractRequest $request)
    {
        parent::__construct($repository, $request);
    }

    public function setHandlers($requestUnit = null): Generator
    {
        $this->requester = (yield $requestUnit) ?? $this->requester;
        $this->executor = fn():Generator => yield from $this->request();
        $this->foundHandler = (yield $requestUnit) ?? $this->foundHandler;
        $this->executor = fn():Generator => yield from $this->handleFound();
        $this->notFoundHandler = (yield $requestUnit) ?? $this->notFoundHandler;
        $this->executor = fn():Generator => yield from $this->handleFoundAndUnFound();
    }



    protected function doExecute()
    {
        if ($fail = $this->executeRequestAndHandle()) {
            Gen::settle($this->setHandlers(), $this->notFoundHandler->getExecutors());
            $this->executeRequestAndHandle();
        }
    }

    protected function executeRequestAndHandle(): bool
    {
        $generator = Gen::spin(($this->executor)());
        return (bool) $generator->getReturn();
    }


    protected function request(): Generator
    {
        yield from $this->requester->get($this->request);
    }


    protected function handleFound(): Generator
    {
        /** @var AbstractProduct $product */
        foreach ($this->request() as $product) {
            $this->foundHandler->handle($product, $this->request);
            yield $product;
        }
    }


    protected function handleFoundAndUnFound(): Generator
    {
        foreach ($this->handleFound() as $handledProduct) {
            yield $handledProduct;
        }
        return $this->notFoundHandler->repeatExecuting($this->request);
    }


}