<?php

namespace App\command;

use Doctrine\Common\Collections\Collection;
use App\base\Request;

class DefaultCommand extends Command
{
    protected function doExecute(Request $request, Collection $blockCollection)
    {
        $blockCollection->map(function ($block) {

        });
    }
}
