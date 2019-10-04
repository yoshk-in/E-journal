<?php


namespace App\console\render;


interface Format
{
    const TIME = 'd-m-Y H:i';
    const PRODUCT_NUMBER = ' Номер [ %s ]';
    const PRODUCT_NAME = ' Блок %s';
    const STAT_INFO = '%s - %s штук: ';
    const STAT_TITLE = 'Всего:' . PHP_EOL . 'Всего %s штук';
    const FULL_INFO = 'процедура %s, вр.начала: %s, вр.завершения: %s';
    const COMPOSITE_CONJUCTION = ';   * вложенные процедуры: ';
    const EOL = PHP_EOL;
    const SHORT_INFO = '%s - %s';


}