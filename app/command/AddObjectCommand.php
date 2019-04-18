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
        $dberrors = [];
        $offset = 0;
        $entityManager = AppHelper::getEntityManager();
        foreach ($range as $unit) {
            $object = new G9($unit);
            $objects[] = $object;
            $object->setStatement('writeInBD');
            try {
            $entityManager->persist($object);
            $entityManager->flush();
            } catch (\Exception $e) {
                $dberrors[] = $e->getMessage();
                array_splice($blocks, $offset);
            }
            ++$offset;
        }
        if (!empty($blocks)) {
            $request->setFeedback(
                "в журнал занесены следующие блоки:"
            );
        }
        foreach ($blocks as $block) {
            $request->setFeedback($block);
        }
        if (!empty($dberrors)) {
            $request->setFeedback(
                "номера следующих блоков не записаны в журнал со следующей ошиюкой:"
            );
            foreach ($dberrors as $error) {
                $request->setFeedback($error);
            }
        }
    }
}
