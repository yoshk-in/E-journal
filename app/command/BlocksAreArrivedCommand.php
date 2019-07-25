<?php


namespace App\command;

use App\base\Request;
use Doctrine\Common\Collections\Collection;

class BlocksAreArrivedCommand extends Command
{

    protected function doExecute(Collection $blockCollection)
    {
        $numbers = $this->request()->getBlockNumbers();
        if (!$this->numbersCountEqCollCount($numbers, $blockCollection)) {
            $newNumbers = $this->getNotPersistedNumbers($numbers, $blockCollection);
            $newBlocks = $this->createAndPersistNewObjects($newNumbers);
            $blockCollection = $this->mergeNewAndOldCollection($newBlocks, $blockCollection);
        }
        $output_info_array = $this->startProcedure($blockCollection);
        $this->addFeedback( "в журнал занесены следующие блоки:", $output_info_array);
    }

    private function createAndPersistNewObjects(array $newBlocks)
    {
        $objects = [];
        foreach ($newBlocks as $newBlock) {
            $target = $this->targetClass;
            $object = new $target();
            $object->initByNumber($newBlock);
            $objects[] = $object;
            $this->entityManager->persist($object);
        }
        return $objects;
    }

    private function startProcedure(Collection $blockCollection)
    {
        if ($tt_name = $this->request()->getTTCommand()) {
            $output_info = $blockCollection->map(function ($block) use ($tt_name) {
                return $block->startTTProcedure($tt_name);
            });
        } else {
            $output_info = $blockCollection->map(function ($block) {
                return $block->startProcedure();
            });
        }
        return $output_info->toArray();
    }

    private function mergeNewAndOldCollection(array $newObjects, Collection $oldCollection)
    {
        array_map(function ($newBlock) use ($oldCollection) {
            $oldCollection->add($newBlock);
        }, $newObjects);
        return $oldCollection;
    }



}