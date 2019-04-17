<?php

namespace App\command;

use App\base\Request;
use App\base\AppHelper;
use App\domain\G9;

class AddObjectCommand extends Command
{
    protected function doExecute(Request $request)
    {
        $blocks = $request->getBlockNumbers();
        $max = max($blocks);
        $min = min($blocks);
        $range = range($min, $max);
        $objects = [];
        $entityManager = AppHelper::getEntityManager();
        foreach ($range as $unit) {
            $object = new G9($unit);
            $objects[] = $object;
            $object->setStatement('writeInBD');
            $entityManager->persist($object);
            $entityManager->flush();
        }
        $request->setFeedback(
            "в журнал занесены следующие блоки:"
            );
        foreach ($blocks as $block) {
            $request->setFeedback($block);
        }

    }
}
