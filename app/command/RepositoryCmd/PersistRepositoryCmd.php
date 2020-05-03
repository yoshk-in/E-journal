<?php


namespace App\command\RepositoryCmd;


class PersistRepositoryCmd extends RepositoryCmd
{
    protected function doExecute()
    {
        parent::doExecute();
        $this->productRepository->save();
    }

}