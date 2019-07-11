<?php


namespace App\command;

use App\base\Request;
use Doctrine\Common\Collections\Collection;

class BlocksAreArrivedCommand extends Command
{

    protected function doExecute(Request $request, Collection $blockCollection)
    {
        $numbers = $request->getBlockNumbers();
        if (!$this->compareCollAndNumbersCount($numbers, $blockCollection)) {
            $newBlocks = array_filter($numbers, function ($number) use ($blockCollection) {
               return !$blockCollection->exists(function($key) use ($number, $blockCollection) {
                   return $blockCollection[$key]->getId() === $number;
                });
            });
            $newBlocks = $this->addObjectCommand($newBlocks);
            array_map(function ($newBlock) use ($blockCollection) {
                $blockCollection->add($newBlock);
            }, $newBlocks);
        }
        $this->startProcedure($request, $blockCollection);
    }

    private function addObjectCommand(array $newBlocks)
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

    private function startProcedure(Request $request, Collection $blockCollection)
    {
        if ($tt_name = $request->getTTCommand()) {
            $blockCollection->map(function ($block) use ($tt_name) {
                $block->startTTProcedure($tt_name);
            });
        } else {
            $blockCollection->map(function ($block) {
                $block->startProcedure();
            });
        }
    }

}