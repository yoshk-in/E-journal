<?php


namespace App\command;


use Doctrine\Common\Collections\Collection;

class BlocksAreDispatchedCommand extends Command
{
    protected function doExecute(Collection $blockCollection)
    {
        $this->ensureRightInput(
            $this->numbersCountEqCollCount($this->request()->getBlockNumbers(), $blockCollection),
            ' данные блоки еще не поступали на прозвону(или настройку) '
            );
        $output_info_array = $blockCollection->map(function ($block) {
            return $block->endProcedure();
        })->toArray();
        $this->addFeedback('отмечены следующие события: ', $output_info_array);
    }
}