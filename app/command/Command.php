<?php

namespace App\command;

use \App\base\AppHelper;

use App\base\exceptions\IncorrectInputException;
use \App\base\Request;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Collection;

abstract class Command
{
    protected $entityManager;

    protected $repo;

    protected $targetClass;

    final public function __construct()
    {
        $this->entityManager = AppHelper::getEntityManager();

    }

    public function execute(Request $request)
    {
        $target = $request->getProperty('targetClass');
        $this->targetClass = $this->getTargetClass($target);
        $this->repo = $this->entityManager->getRepository($this->targetClass);
        $numbers = $request->getBlockNumbers();
        $criteria = Criteria::create();
        foreach ($numbers as $number) {
            $criteria->orWhere(Criteria::create()->expr()->eq('id', $number));
        }
        $blockCollection = $this->repo->matching($criteria);
        $this->addFeedback($request, $blockCollection);
        $this->doExecute($request, $blockCollection);
        $this->entityManager->flush();
        echo static::class ."\n";

    }

    protected function compareCollAndNumbersCount(array $numbers, Collection $blockCollection)
    {
        if ($blockCollection->count() !== count($numbers)) return false;
        return true;
    }

    protected function getTargetClass(string $name)
    {
        return '\App\domain\\'.$name;
    }

    protected function ensureRightInput(bool $condition, string $msg = '')
    {
        if (!$condition) throw new IncorrectInputException('неверно заданы параметры запроса: ' . $msg);
    }


    protected function addFeedback(Request $request, Collection $blockCollection)
    {
        if (!$blockCollection->isEmpty()) {
            $request->setFeedback(
                "в журнал занесены следующие блоки:"
            );
        }
    }

    abstract protected function doExecute(Request $request, Collection $blockCollection);

}

