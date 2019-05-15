<?php

namespace App\command;

use App\base\Request;

class PrintRangeStatCommand extends Command
{
    protected function doExecute(Request $request)
    {
        $numbers = $request->getBlockNumbers();

        $blocks = [];

        foreach ($numbers as $number) {
            $blocks[] = $this->entityManager->find('\App\domain\G9', $number);

        }

        if (is_null($blocks[0])) {
            throw new \App\base\AppException('данные на эти номера отсутствуют');

        }

        foreach ($blocks as $block) {
            $request->setFeedback(
                (string) $block->getNumber().' - на стадии: '.$block->getStatement()
            );

        }

    }
}

