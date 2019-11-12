<?php


namespace App\repository;


use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;


class DoctrineORMAdapter
{

    private $_em;
    private $servicedEntity;
    private $docRep;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->_em = $entityManager;
    }

    public function setServicedEntity($entity)
    {
        $this->servicedEntity = $entity;
        $this->docRep = $this->_em->getRepository($this->servicedEntity);
    }


    public function save()
    {
        $this->_em->flush();
    }

    public function persist(object $object)
    {
        $this->_em->persist($object);
    }

    public function findEntityById(string $entityClass, $id)
    {
        return $this->_em->getRepository($entityClass)->find($id);
    }


    public function findWhere(array $criteria, array $order): array
    {
        return $this->docRep->findBy($criteria, $order);
    }

    public function findOneWhere(array $criteria, array $order)
    {
        return $this->docRep->findOneBy($criteria, $order);
    }

    public function findAll(array $criteria, array $order): array
    {
        return $this->docRep->findBy($criteria, $order);
    }


}