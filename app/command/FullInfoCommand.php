<?php

namespace App\command;

use App\base\AppHelper;


class FullInfoCommand extends Command
{
    private $procFeedback = [];
    private $stateNames = [];

    protected function doExecute(
        \ArrayAccess $collection,
        $repository,
        $domainClass,
        $productName,
        ?array $not_found = null,
        ?string $procedure = null
    ): array
    {
        foreach ($collection as $product) {
            $output[$productName] = $product->getInfo();
        }
        return [
            "информация по найденным блокам: \n" => $output ?? null,
        ];

//        foreach ($info as $part) foreach ($part as $scratch) var_dump($scratch);
//        $this->setForeachBlockInfo($blocksCollection);
//        $this->addFeedback('информация по несданным блокам:', $this->getBlockInfo());
//        $total_count_block_info = $this->getStateNamesWithCountBlocks($this->getProcNames());
//        $this->addFeedback('кол-во блоков в работе: ' . $blocksCollection->count(), $total_count_block_info);
    }


    private function getStateNamesWithCountBlocks(array $stateArr): array
    {
        foreach ($stateArr as $block_id => $state_name) {
            if (!isset($states[$state_name])) {
                $state = new \stdClass();
                $state->count = 1;
                $state->ids_str = $block_id;
                $states[$state_name] = $state;
            } else {
                ++$states[$state_name]->count;
                $states[$state_name]->ids_str .= ', ' . $block_id;
            }
        }

        foreach ($states as $state_name => $state)
            $info[] = $state_name . ":   $state->count штук.:  " . $state->ids_str;

        return $info;
    }


    private function getForeachProcInfoLine(array $info_array): string
    {
        $info_string = '';
        foreach ($info_array as $key => $proc) {
            if ($key === 1) $info_string .= '  ---  завершенные испытания: ';
            $info_string .= $proc->getInfo('short');
        }
        return $info_string;
    }

    private function setForeachBlockInfo(Collection $blocksCollection): void
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
        return [(int)$block->getId(), $state_name, $executing_proc_info];
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

