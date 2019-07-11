<?php

namespace App\command;

use App\base\exceptions\AppException;
use App\base\Request;
use Doctrine\Common\Collections\Collection;
use App\base\AppHelper;


class AddObjectCommand extends Command
{
    protected function doExecute(Request $request, ?Collection $blockCollection)
    {
        $blocks   = $request->getBlockNumbers();

        $max      = max($blocks);
        $min      = min($blocks);
        $range    = range($min, $max);
        $objects  = [];
        $dberrors = [];
        $offset   = 0;
        $lastObject = $this->repo->findOneBy(array(), ['id' => 'desc']);
        if (is_null($lastObject)) {
            $partNumber = (AppHelper::getCacheObject())->getPartNumber();
            if (is_null($partNumber)) throw new AppException("установите сначала номер партии командой вида: \n
            г9 партия=120\n
            отсчет номеров блоков в журнале начнется с 'номер партии'001");
            $startNumber = (string) $partNumber . '001';
            $range = range($startNumber, $max);
        } else  {
            if ($lastObject->getId() < $min) {
                $min = $lastObject->getid();
                $range = range(++$min, $max);
            } else {
                $request->setFeedback('данные номера уже добавлены в журнал');
                return;
            }
        }

        foreach ($range as $unit) {
            $target = $this->targetClass;
            $object    = new $target();
            $object->initByNumber($unit);
            $objects[] = $object;
            $this->entityManager->persist($object);

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
//        if (!empty($dberrors)) {
//            $request->setFeedback(
//                "номера следующих блоков не записаны в журнал со следующей ошиюкой:"
//            );
//            foreach ($dberrors as $error) {
//                $request->setFeedback($error);//           }
//        }
    }
}

