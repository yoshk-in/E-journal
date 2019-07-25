<?php

namespace App\command;


use App\domain\TechProcedure;
use App\domain\Procedure;
use Doctrine\Common\Collections\Collection;

class PrintRangeStatCommand extends Command
{
    protected function doExecute(Collection $blockCollection)
    {
        $numbers = $this->request()->getBlockNumbers();
        $result = $blockCollection->map(function ($block) {
            $procedures = $block->getProcCollection();
            $tt_procedures= $block->getTTCollection();
            $proc_entries = $this->getInfoOfExistProcs($procedures);
            $tt_proc_entries = $this->getInfoOfExistProcs($tt_procedures);
            return [$block->getId() => array_merge($proc_entries, $tt_proc_entries)];
        })->toArray();
        $this->addFeedback("информация по найденным блокам: \n");
        array_map(function ($infoPart) {
            foreach ($infoPart as $blockNumber => $blockInfo) {
                $this->addFeedback("Номер " . $blockNumber . "\n", $blockInfo);
                $this->addFeedback("\n");
            }
        }, $result);
        if (!$this->numbersCountEqCollCount($numbers, $blockCollection)) {
            $not_existed_numbers = $this->getNotPersistedNumbers($numbers, $blockCollection);
            $this->addFeedback("о данных номерах нет записей в журнале:\n", $not_existed_numbers);
        }

    }

    private function getInfoOfExistProcs(Collection $procs)
    {
        return $procs->filter(function (Procedure $proc) {
            if ($proc->getStart()) return true;
        })->map(function (Procedure $proc) {
            if ($proc instanceof TechProcedure) return '             > ' . $proc->getInfo();
            return $proc->getInfo('all');
        })->toArray();
    }
}

