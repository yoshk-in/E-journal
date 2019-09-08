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

    public function findConcreteProducts(Criteria $prevCriteria, string $idFieldName, array $numbers): array
    {
        foreach ($numbers as $number) {
            $prevCriteria->andWhere(Criteria::create()->expr()->eq($idFieldName, $number));
        }
        $collection = $this->getDoctrineRepository()->matching($prevCriteria);
        $not_found = array_filter($numbers, function ($number) use ($collection) {
            return !$collection->exists(function ($key) use ($number, $collection) {
                return $collection[$key]->getNumber() == $number;
            });
        });
        return [$collection, $not_found];
    }

    public function findNotFinishedProducts(
        Criteria $fistCriteria,
        string $fieldName,
        string $fieldValue
    ): Collection
    {
        $criteria = $fistCriteria->orWhere(Criteria::create()->expr()->lt($fieldName, $fieldValue));
        return $this->getDoctrineRepository()->matching($criteria);
    }

    public function save()
    {
        $this->getEntityManager()->flush();
    }

    public function persist(object $object)
    {
        $this->getEntityManager()->persist($object);
    }

    public function andCriteria(string $fieldName, string $fieldValue): Criteria
    {
        return $this->getDoctrineCriteria()::create()->andWhere(Criteria::create()->expr()
            ->eq($fieldName, $fieldValue));
    }


    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    protected function getDoctrineCriteria(): string
    {
        return $this->doctrineCriteria;
    }

    protected function getDoctrineRepository(): ObjectRepository
    {
        return $this->doctrineRepository;
    }

}