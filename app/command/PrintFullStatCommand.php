<?php

namespace App\command;

use App\domain\Product;
use Doctrine\Common\Collections\Collection;

class PrintFullStatCommand extends Command
{
    private $procFeedback = [];
    private $stateNames = [];

    protected function doExecute(Collection $blocksCollection)
    {
        $this->setForeachBlockInfo($blocksCollection);
        $this->addFeedback('информация по несданным блокам:', $this->getBlockInfo());
        $total_count_block_info = $this->getStateNamesWithCountBlocks($this->getProcNames());
        $this->addFeedback('кол-во блоков в работе: ' . $blocksCollection->count(), $total_count_block_info);
    }

    private function getStateNamesWithCountBlocks(array $origArr) : array
    {
        $total_count = [];
        $result_info = [];
        foreach ($origArr as $block_id => $state_name) {
            if (array_key_exists($state_name, $total_count)) {
                ++$total_count[$state_name][0];
                $total_count[$state_name][1] = $total_count[$state_name][1] . ", $block_id";
            }
            else {
                $total_count[$state_name][0] = 1;
                $total_count[$state_name][1] = $block_id;
            }
        }
        foreach ($total_count as $state_name => $state_info) $result_info[] = $state_name . ":   $state_info[0] штук.:  " .$state_info[1];

        return $result_info;
    }


    private function getForeachProcInfoLine(array $info_array) : string
    {
        $info_string = '';
        foreach ($info_array as $key => $proc) {
            if ($key === 1) $info_string .= '  ---  завершенные испытания: ';
            $info_string .= $proc->getInfo('short');
        }
        return $info_string ;
    }

    private function setForeachBlockInfo(Collection $blocksCollection) : void
    {
        $blocksCollection->map(function ($block) {
            list($blocId, $state_name, $executing_procs) = $this->getCurrentProcsInfo($block);
            $info_string = $this->getForeachProcInfoLine($executing_procs);
            $feedback = 'Номер ' . $block->getId() . ' текущая процедура: ' . $info_string;
            $this->setStateName($blocId, $state_name);
            $this->setProcFeedback($feedback);
        });
    }

    private function getCurrentProcsInfo(Product $block): array
    {
        $executing_proc_info = [];
        $current_proc = $block->getCurrentProc();
        if ($current_proc->getStart()) {
        } else {
            $proc_collection = $block->getProcCollection();
            $current_proc = $proc_collection[$block->getCurrentProcId() - 1];
        }
        $executing_proc_info[] = $current_proc;
        if ($current_proc->isFinished()) $option = 'next_state';
        else $option = 'ru';
        $state_name = $block->getProcedureList($option)[$current_proc->getName()];
        if ($block->isCompositeProc($current_proc)) {
            $tt_collection = $block->getTTCollection();
            $tt_collection->map(function ($tt_proc) use (&$executing_proc_info) {
                if ($tt_proc->getStart()) {
                    $executing_proc_info[] = $tt_proc;
                }
            });
        }
        return [(int) $block->getId(), $state_name, $executing_proc_info];
    }

    private function getBlockInfo(): array
    {
        return $this->procFeedback;
    }

    private function setProcFeedback(string $procFeedback): void
    {
        $this->procFeedback[] = $procFeedback;
    }

    private function getProcNames(): array
    {
        return $this->stateNames;
    }

    private function setStateName(int $blockId, string $stateName): void
    {
        $this->stateNames[$blockId] = $stateName;
    }
}

