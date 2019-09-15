<?php


namespace App\domain;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;


class ORM
{
    private $doctrineCriteria;
    private $entityManager;
    private $doctrineRepository;

    public function __construct(EntityManagerInterface $entityManager, string $domainClass)
    {
        $this->entityManager = $entityManager;
        $this->doctrineRepository = $this->entityManager->getRepository($domainClass);
        $this->doctrineCriteria = Criteria::class;
    }

    public function findWhereEach(Criteria $prevCriteria, string $fieldName, array $values, string $option): Collection
    {
        switch ($option) {
            case 'or': $where = 'orWhere'; break;
            case 'and': $where = 'andWhere';
        }

        foreach ($values as $value) {
            $prevCriteria->$where(Criteria::create()->expr()->eq($fieldName, $value));
        }
        return $this->doctrineRepository->matching($prevCriteria);
    }


    public function save()
    {
        $this->entityManager->flush();
    }

    public function persist(object $object)
    {
        $this->entityManager->persist($object);
    }

    public function whereCriteria(string $fieldName, string $fieldValue): Criteria
    {
        return $this->doctrineCriteria::create()->where(Criteria::create()->expr()
            ->eq($fieldName, $fieldValue));
    }



}