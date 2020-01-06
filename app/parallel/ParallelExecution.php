<?php


namespace App\parallel;

use App\domain\PartialProcedure;
use App\events\{ISubscriber};

class ParallelExecution implements ISubscriber
{
    public function update($observable, string $event)
    {
        if ($observable instanceof PartialProcedure) {
            echo 'there is need to parallel execution' . PHP_EOL;
        }
//        if ($observable instanceof PartialProcedure) {
//            $php = '/usr/bin/php';
//            $exec = '/home/dee/workspace/mmz/journal/E-journal/app/parallel/reminder.php';
//            if (file_exists($php) && file_exists($exec)) {
//                $result = `$php $exec`;
//                echo $result;
//            } else echo 'not exist' . PHP_EOL;
//        }
//        throw new \Exception();
    }

    public function subscribeOn(): array
    {
        // TODO: Implement subscribeOn() method.
    }
}