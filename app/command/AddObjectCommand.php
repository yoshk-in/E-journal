<?php

namespace App\command;

use App\base\exceptions\AppException;
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
        $lastObject = $this->repo->findOneBy(array(), ['number' => 'desc']);
        if (is_null($lastObject)) {
            $partNumber = (\App\base\AppHelper::getCacheObject())->getPartNumber();
            if (is_null($partNumber)) throw new AppException("установити сначала номер партии командой вида: \n
            г9 партия=120\n
            отсчет номеров блоков в журнале начнется с 'номер партии'001");
            $startNumber = (string) $partNumber . '001';
            $range = range($startNumber, $max);
        } else  {
            if ($lastObject->getNumber() <= $min) {
                $min = $lastObject->getNumber();
                $range = range(++$min, $max);
            }
        }

        foreach ($range as $unit) {
            $object    = new G9($unit);
            $objects[] = $object;
            $object->setStatement('writeInBD');
            $this->entityManager->persist($object);

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
//        if (!empty($dberrors)) {
//            $request->setFeedback(
//                "номера следующих блоков не записаны в журнал со следующей ошиюкой:"
//            );
//            foreach ($dberrors as $error) {
//                $request->setFeedback($error);//           }
//        }
    }
}

