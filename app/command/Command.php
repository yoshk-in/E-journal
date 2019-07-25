<?php

namespace App\command;

use \App\base\AppHelper;

use App\base\exceptions\IncorrectInputException;
use App\base\Request;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Collection;

abstract class Command
{
    protected $entityManager;

    protected $repo;

    protected $targetClass;

    protected $request;

    final public function __construct()
    {
        $this->entityManager = AppHelper::getEntityManager();
    }

    public function execute(Request $request)
    {
        $this->request = $request;
        $this->targetClass = $this->getTargetClass();
        $this->repo = $this->entityManager->getRepository($this->targetClass);
        $numbers = $request->getBlockNumbers();
        $blockCollection = $this->findByCriteria($numbers);
        $this->doExecute($blockCollection);
        $this->entityManager->flush();
        echo static::class . "\n";

    }


    protected function addFeedback(string $title, ?array $messages = null)
    {
        $this->request()->setFeedback($title);
        is_null($messages) ?: array_map(function ($string) {
            $this->request()->setFeedback($string);
        }, $messages);
    }

    protected function findByCriteria(?array $numbers = null)
    {
        $target = $this->getTargetClass();
        $criteria = Criteria::create();
        list($current_proc_id_value, $current_proc_id_name, $id_name) = $target::getClassTabledata();
        if (is_null($numbers)) {
            $criteria->orWhere(Criteria::create()->expr()->lt($current_proc_id_name, $current_proc_id_value));
            $blockCollection = $this->repo->matching($criteria);
        } else {
            foreach ($numbers as $number) {
                $criteria->orWhere(Criteria::create()->expr()->eq($id_name, $number));
            }
            $blockCollection = $this->repo->matching($criteria);
        }
        return $blockCollection;
    }


    protected function getNotPersistedNumbers(array $numbers, $blockCollection)
    {
        $new_numbers = array_filter($numbers, function ($number) use ($blockCollection) {
            return !$blockCollection->exists(function ($key) use ($number, $blockCollection) {
                return $blockCollection[$key]->getId() === $number;
            });
        });
        return $new_numbers;
    }

    protected function numbersCountEqCollCount(array $numbers, Collection $blockCollection)
    {
        if ($blockCollection->count() !== count($numbers)) return false;
        return true;
    }

    protected function getTargetClass()
    {
        return $this->request->getProperty('targetClass');
    }

    protected function request()
    {
        return $this->request;
    }

    protected function ensureRightInput(bool $condition, string $msg = '')
    {
        if (!$condition) throw new IncorrectInputException('неверно заданы параметры запроса: ' . $msg);
    }

    abstract protected function doExecute(Collection $blockCollection);

}

