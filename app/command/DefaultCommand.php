<?php

namespace App\command;

use Doctrine\Common\Collections\Collection;


class DefaultCommand extends RepositoryCommand
{
    protected function doExecute(\ArrayAccess $collection, ?array $not_found = null, ?string $command = null)
    {
    }
}
