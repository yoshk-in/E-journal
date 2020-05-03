<?php


namespace App\repository;


use App\domain\AbstractProduct;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;


class DBLayer
{

    private EntityManagerInterface $_em;
    private ObjectRepository $currentRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->_em = $entityManager;
    }

    public function setServicedEntity($entity)
    {
        $this->currentRepository = $this->_em->getRepository($entity);
    }

    public function remove($entity)
    {
        $this->_em->remove($entity);
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


    public function findWhere(array $criteria, array $order, ?int $limit = null, ?int $offset = null): array
    {
        return $this->currentRepository->findBy($criteria, $order, $limit, $offset);
    }

    /**
     * @param array $criteria
     * @return object|null|AbstractProduct
     */
    public function findOneWhere(array $criteria)
    {
        return $this->currentRepository->findOneBy($criteria);
    }



}