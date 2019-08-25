<?php


namespace App\console;


use Doctrine\Common\Collections\Collection;

class Render
{
    private $commandMap = [
        'информация по несданным блокам:'
    ];



    public function renderCommand(array $info)
    {
        [$title, $infoColl] = $info;
        foreach ($infoColl as $block) {
            [$blockInfo, $procInfo] = $block;
            [$block_name, $number] = $blockInfo;
            foreach ($procInfo as $proc) {
                [$name, $start, $end, $finished, $inners] = $proc;
            }
        }
        var_dump($title, $block_name, $number, $name, $start, $end, $finished ? 'завершено' : "завершится", $inners ?? null);
    }






    private function string(?string $string = null)
    {
        return $string . "\n";
    }

    private function timeToString(\DateTime $time)
    {
        return $time->format('Y-m-d H:i');
    }
}