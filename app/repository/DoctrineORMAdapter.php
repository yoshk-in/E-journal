<?php


namespace App\repository;


use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use \Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\Tools\SchemaTool;


class DoctrineORMAdapter
{

    private $_em;
    private $servicedEntity;
    private $docRep;
    private $criteria;




    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->_em = $entityManager;
        $this->criteria = Criteria::class;
    }

    public function setServicedEntity($entity)
    {
        $this->servicedEntity = $entity;
        $this->docRep = $this->_em->getRepository($this->servicedEntity);
    }


    public function findWhereEach(Comparison $prevWhere, string $fieldName, array $values): Collection
    {

        $criteria = $this->criteria::create();
        foreach ($values as $value) {
            $criteria->orWhere($criteria::expr()->eq($fieldName, $value));
        }

        return $this->docRep->matching($criteria->andWhere($prevWhere));
    }


    public function save()
    {
        $this->_em->flush();
    }

    public function persist(object $object)
    {
        $this->_em->persist($object);
    }

    public function whereProperty(string $fieldName, string $fieldValue): Comparison
    {
        return $this->criteria::expr()->eq($fieldName, $fieldValue);
    }




}