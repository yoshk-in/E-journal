<?php


namespace App\command;


use App\base\Request;
use Doctrine\Common\Collections\Criteria;

class BlocksAreArrivedCommand extends Command
{
    protected function doExecute(Request $request)
    {
        $numbers = $request->getBlockNumbers();
        $criteria = Criteria::create();
        $criteria->where($criteria->expr()->contains('id', $numbers[0]));
        $blockCollection = $this->repo->findBy($numbers);
        var_dump($blockCollection);
    }
}