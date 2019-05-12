<?php

namespace tests;

use App\base\AppException;
use \App\domain\mockG9;

require_once 'bootstrapTests.php';
function myPrint(string $msg)
{
    echo $msg . "\n";
}

function exceptionCatcher(Object $object,string $method) {
    try {
        ob_start();
        var_dump($object, true);
        $dump = ob_get_clean();
        $object->$method();
        myPrint('ОШИБКА В ТЕСТОВЫХ УСЛОВИЯХ НЕ СРАБОТАЛА!');
        myPrint('СОСТОЯНИЕ ОБЪЕКТА ДО ВЫЗОВА МЕТОДА');
        var_dump($dump);
        myPrint('СОСТОЯНИЕ ПОСЛЕ');
        var_dump($object);
        myPrint('');
    } catch (AppException $exception) {
        echo 'ЗАПЛАНИРОВАННАЯ ТЕСТОВАЯ ОШИБКА: ' .
            $exception->getMessage() . "\n";
    }
}

$g9 = new mockG9(120051);
$g9->nextProcedure();
myPrint($g9->getProcedureProp('name'));
//проверка срабатавания исключения при вызове события до положенного момента времени
exceptionCatcher($g9, 'endProcedure');

sleep(1);
$g9->endProcedure();
myPrint($g9->getProcedureProp('name'));
//var_dump($g9->getProcedureProp('nastroy', 'start'));
//проверка срабатывания исключения при попытке записи уже установленного события
exceptionCatcher($g9, 'endProcedure');
$g9->nextProcedure();
myPrint($g9->getProcedureProp('name'));
//проверка срабатывания исключения при попытке завершить событие до завершения техпроцедур
exceptionCatcher($g9, 'nextProcedure');
exceptionCatcher($g9, 'endProcedure');

sleep(1);

//НЕ СРАБАТЫВАЕТ ИСКЛЮЧЕНИЕ - РАЗРУЛИТЬ ТРАБЛ!!!
myPrint($g9->getProcedureProp('name'));
exceptionCatcher($g9, 'endProcedure');
myPrint($g9->getProcedureProp('start')->format('H:i:s'));



//var_dump($g9->getProcedureEnd('vibro'));
