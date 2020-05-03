<?php


namespace App\CLI\render;


interface Format
{
    const TIME = 'd-m-Y H:i';
    const PRODUCT_NUMBER = ' Номер [ %s ]';
    const PRODUCT_NAME = ' Блок %s';
    const STAT = '%s - %s штук: %s';
    const STAT_TITLE = 'Всего %s штук';
    const FULL = "| %'_-25s вр.начала: %s, вр.завершения: %s";
    const PARTIAL_TITLE = '   <вложенные процедуры: ';
    const EOL = PHP_EOL;
    const SHORT = ' %s вр. заверш: %s ';
    const COUNT_DELIMITER = ', ';
    const NONE = ' - ';
}