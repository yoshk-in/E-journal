<?php


namespace App\command\RepositoryCmd\foundHandler;


use App\base\AbstractRequest;
use App\base\exceptions\ProcedureException;
use App\domain\AbstractProduct;
use App\domain\procedures\interfaces\NameStateInterface;

abstract class FoundHandler
{
    const ERR_MSG = ' текущее состояние: %s ';


    public function exceptionLessHandle(callable $handle, NameStateInterface $product)
    {
        try {
            $handle();
        } catch (ProcedureException $exception) {
            if ($product->isEnded()) {
                $exception->additionalMessage(sprintf(self::ERR_MSG, $product->getStateName()));
            }
            throw $exception;
        }
    }

    public function __invoke(NameStateInterface $product, AbstractRequest $request)
    {
        return $this->handle($product, $request);
    }

    abstract public function handle(NameStateInterface $product, AbstractRequest $request);

}