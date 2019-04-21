<?php

namespace App\command;

use App\base\AppException;
use App\base\Request;

use App\domain\G9;

class AddObjectCommand extends Command
{
    protected function doExecute(Request $request)
    {
        $blocks   = $request->getBlockNumbers();

        $max      = max($blocks);
        $min      = min($blocks);
        $range    = range($min, $max);
        $objects  = [];
        $dberrors = [];
        $offset   = 0;
        $last = $this->repo->findOneBy(array(), ['number' => 'desc']);
        if (is_null($last)) {
            $partNumber = \App\cache\Cache::getPartNumber();
            if (is_null($partNumber)) throw new AppException("установить сначала номер партии командой вида: \n
            г9 партия=120\n
            отсчет номеров блоков в журнале начнется с 'номер партии'001");
            $startNumber = (string) $partNumber . '001';
            $range = range($startNumber, $max);
        }
        if ($last < $min)

        foreach ($range as $unit) {
            $object    = new G9($unit);
            $objects[] = $object;
            $object->setStatement('writeInBD');
            try {
                $this->entityManager->persist($object);

            } catch (\Exception $e) {
                $dberrors[] = $e->getMessage();
                array_splice($blocks, $offset);
            }
            ++$offset;
        }
        $this->entityManager->flush();
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

