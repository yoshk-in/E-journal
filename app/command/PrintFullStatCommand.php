<?php

namespace App\command;

use App\base\Request;

class PrintFullStatCommand extends Command
{
    protected function doExecute(Request $request)
    {
        $blocks = $this->repo->findAll();
        if (is_null($blocks)) {
            $request->setFeedback('нет записей');
            return;
        }

        foreach ($blocks as $block) {
            $request->setFeedback((string) $block->getNumber().' - на стадии: '.$block->getStatement());

        }
    }
}

