<?php


namespace App\command;


use App\base\Request;
use Doctrine\Common\Collections\Collection;

class BlocksAreDispatchedCommand extends Command
{
    protected function doExecute(Request $request, Collection $blockCollection)
    {
        $this->ensureRightInput(
            $this->compareCollAndNumbersCount($request->getBlockNumbers(), $blockCollection),
            ' данные блоки еще не поступали на прозвону(или настройку) '
            );
        $blockCollection->map(function ($block) {
            $block->endProcedure();
        });
    }
}