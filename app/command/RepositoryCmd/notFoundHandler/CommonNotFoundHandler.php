<?php


namespace App\command\RepositoryCmd\notFoundHandler;


use App\base\exceptions\WrongInputException;
use App\command\RepositoryCmd\RepositoryCmd;
use Generator;

class CommonNotFoundHandler extends NotFoundHandler
{

    public function repeatExecuting($request): bool
    {
        if (!empty($dataArray = $request->getRequestingData())) {
            ob_start();
            print_r($dataArray);
            $data = ob_get_clean();
            WrongInputException::create('введите номер: запрашиваемые данные: ' . PHP_EOL . $data);
            return true;
        }
        return false;
    }

    public function getExecutors(): array
    {
        return [];
    }
}